<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CashLedger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class VerifyConcurrency extends Command
{
    protected $signature = 'growpos:verify-concurrency';

    protected $description = 'Run two real checkout processes against the last stock in an isolated MySQL database';

    public function handle(): int
    {
        $sourceName = DB::getDefaultConnection();
        $source = DB::connection();
        if ($source->getDriverName() !== 'mysql') {
            $this->error('MySQL is required for this test.');

            return self::FAILURE;
        }
        $database = 'growpos_verify_'.bin2hex(random_bytes(12));
        $directory = storage_path('app/private/probes/'.$database);
        mkdir($directory, 0700, true);
        $created = false;
        $workers = [];
        try {
            $source->statement('CREATE DATABASE `'.$database.'`');
            $created = true;
            config(['database.connections.probe' => array_replace($source->getConfig(), ['name' => 'probe', 'database' => $database, 'url' => null])]);
            DB::setDefaultConnection('probe');
            Schema::clearResolvedInstance('db.schema');
            if (DB::connection()->getDatabaseName() !== $database) {
                throw new \RuntimeException('Isolation guard failed');
            }
            Artisan::call('migrate', ['--database' => 'probe', '--force' => true]);
            DB::setDefaultConnection('probe');
            $owner = User::create(['name' => 'Probe owner', 'email' => 'probe-owner@example.test', 'password' => 'unused-test-only', 'role' => 'admin']);
            $tenant = Tenant::create(['user_id' => $owner->id, 'name' => 'Probe', 'slug' => 'probe', 'business_type' => 'retail', 'email' => 'probe@example.test', 'phone' => '000', 'address' => 'Isolated test']);
            $owner->update(['tenant_id' => $tenant->id]);
            $cashier = User::create(['tenant_id' => $tenant->id, 'name' => 'Probe cashier', 'email' => 'probe-cashier@example.test', 'password' => 'unused-test-only', 'role' => 'kasir']);
            $plan = Plan::create(['name' => 'Growth', 'slug' => 'growth', 'price' => 100000]);
            Subscription::create(['tenant_id' => $tenant->id, 'plan_id' => $plan->id, 'status' => 'active', 'start_date' => now(), 'end_date' => now()->addMonth()]);
            $category = Category::create(['tenant_id' => $tenant->id, 'name' => 'Probe', 'slug' => 'probe']);
            $product = Product::create(['tenant_id' => $tenant->id, 'category_id' => $category->id, 'sku' => 'PROBE', 'product_name' => 'Last unit', 'type' => 'product', 'is_active' => true, 'manage_stock' => true, 'stock' => 1, 'sell_price' => 10000]);
            foreach ([$owner, $cashier] as $user) {
                Shift::create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'start_time' => now()->subMinute(), 'cash_start' => 0, 'cash_expected' => 0, 'status' => 'open']);
                $worker = new Process([PHP_BINARY, base_path('artisan'), 'growpos:checkout-probe', $database, (string) $user->id, (string) $product->id, $directory], base_path());
                $worker->setTimeout(30)->start();
                $workers[] = $worker;
            }
            $deadline = microtime(true) + 15;
            while (count(glob($directory.'/ready-*')) < 2 && microtime(true) < $deadline) {
                usleep(10000);
            }
            touch($directory.'/go');
            $statuses = [];
            foreach ($workers as $worker) {
                $worker->wait();
                if (! $worker->isSuccessful()) {
                    throw new \RuntimeException('Worker failed');
                }
                $statuses[] = trim($worker->getOutput());
            }
            sort($statuses);
            $valid = $statuses === ['200', '422'] && (int) $product->fresh()->stock === 0
                && DB::table('orders')->count() === 1 && DB::table('cash_entries')->sum('amount') == 10000
                && DB::table('stock_movements')->count() === 1;
            if (! $valid) {
                $this->error('FAIL checkout race invariants');

                return self::FAILURE;
            }
            $this->info('PASS two concurrent checkout processes: one sale, one stock rejection, stock zero, one cash entry and one stock movement.');
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            $pending = Order::create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'shift_id' => Shift::where('user_id', $owner->id)->value('id'), 'invoice_number' => 'PROBE-QR', 'subtotal' => 10000, 'grand_total' => 10000, 'payment_method' => 'midtrans', 'payment_status' => 'unpaid', 'order_status' => 'pending']);
            $statuses = $this->race($database, $directory, $product->id, [[$owner->id, 'settle'], [$cashier->id, 'settle']], $pending->id);
            if ($statuses !== ['200', '200'] || DB::table('tenant_wallets')->sum('balance') != 9850 || $pending->fresh()->payment_status !== 'paid') {
                throw new \RuntimeException('Duplicate settlement invariant failed');
            }
            $this->info('PASS simultaneous settlement callbacks credit wallet exactly once.');
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            $product->refresh()->update(['stock' => 1]);
            $ordersBefore = DB::table('orders')->count();
            $statuses = $this->race($database, $directory, $product->id, [[$owner->id, 'checkout'], [$owner->id, 'close']]);
            $closed = Shift::where('user_id', $owner->id)->firstOrFail();
            $salesAdded = DB::table('orders')->count() - $ordersBefore;
            if (! in_array($statuses, [['200', '200'], ['200', '422']], true) || $closed->status !== 'closed' || (float) $closed->cash_expected !== (float) ($closed->cash_start + app(CashLedger::class)->net($closed)) || (int) $product->fresh()->stock !== 1 - $salesAdded) {
                $this->line(json_encode(['statuses' => $statuses, 'shift_status' => $closed->status, 'expected' => $closed->cash_expected, 'ledger' => app(CashLedger::class)->net($closed), 'stock' => $product->fresh()->stock, 'sales_added' => $salesAdded]));
                throw new \RuntimeException('Shift close race invariant failed');
            }
            $this->info('PASS checkout racing shift close: accepted sales included in closing cash, otherwise checkout rejected.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            report($exception);
            $this->error(get_class($exception));
            $this->error('Concurrency verification failed; production data was not modified.');

            return self::FAILURE;
        } finally {
            foreach ($workers as $worker) {
                if ($worker->isRunning()) {
                    $worker->stop();
                }
            }
            DB::setDefaultConnection($sourceName);
            Schema::clearResolvedInstance('db.schema');
            DB::purge('probe');
            if ($created) {
                $source->statement('DROP DATABASE `'.$database.'`');
            }
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            rmdir($directory);
        }
    }

    private function race(string $database, string $directory, int $productId, array $operations, ?int $orderId = null): array
    {
        $workers = [];
        try {
            foreach ($operations as [$userId, $operation]) {
                $args = [PHP_BINARY, base_path('artisan'), 'growpos:checkout-probe', $database, (string) $userId, (string) $productId, $directory, '--operation='.$operation];
                if ($orderId) {
                    $args[] = '--order='.$orderId;
                }
                $worker = new Process($args, base_path());
                $worker->setTimeout(30)->start();
                $workers[] = $worker;
            }
            $deadline = microtime(true) + 15;
            while (count(glob($directory.'/ready-*')) < 2 && microtime(true) < $deadline) {
                usleep(10000);
            }
            touch($directory.'/go');
            $statuses = [];
            foreach ($workers as $worker) {
                $worker->wait();
                if (! $worker->isSuccessful()) {
                    throw new \RuntimeException('Worker failed');
                }
                $statuses[] = trim($worker->getOutput());
            }
            sort($statuses);

            return $statuses;
        } finally {
            foreach ($workers as $worker) {
                if ($worker->isRunning()) {
                    $worker->stop();
                }
            }
        }
    }
}
