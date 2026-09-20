<?php

namespace App\Console\Commands;

use App\Http\Controllers\PosController;
use App\Http\Controllers\ShiftController;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderPaymentService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutProbe extends Command
{
    protected $signature = 'growpos:checkout-probe {database} {user} {product} {barrier} {--operation=checkout} {--order=}';

    protected $description = 'Internal isolated MySQL concurrency worker';

    protected $hidden = true;

    public function handle(): int
    {
        $database = $this->argument('database');
        $barrier = realpath($this->argument('barrier'));
        $root = realpath(storage_path('app/private/probes'));
        if (! preg_match('/^growpos_verify_[a-f0-9]{24}$/', $database) || ! $root || ! $barrier || ! str_starts_with($barrier, $root.DIRECTORY_SEPARATOR)) {
            return self::FAILURE;
        }
        $config = DB::connection()->getConfig();
        config(['database.connections.probe' => array_replace($config, ['name' => 'probe', 'database' => $database, 'url' => null]), 'cache.default' => 'array', 'session.driver' => 'array']);
        DB::setDefaultConnection('probe');
        Auth::setUser(User::findOrFail($this->argument('user')));
        touch($barrier.'/ready-'.getmypid());
        $deadline = microtime(true) + 15;
        while (! is_file($barrier.'/go')) {
            if (microtime(true) > $deadline) {
                return self::FAILURE;
            }
            usleep(10000);
        }
        $request = Request::create('/pos', 'POST', [
            'checkout_key' => (string) Str::uuid(), 'items' => [['id' => (int) $this->argument('product'), 'quantity' => 1]],
            'payment_method' => 'cash', 'payment_status' => 'paid', 'paid_amount' => 10000,
            'grand_total' => 10000, 'order_type' => 'takeaway',
        ]);
        $request->headers->set('Accept', 'application/json');
        app()->instance('request', $request);
        try {
            if ($this->option('operation') === 'settle') {
                $order = Order::findOrFail($this->option('order'));
                app(OrderPaymentService::class)->apply($order->id, ['order_id' => $order->invoice_number, 'gross_amount' => $order->grand_total, 'transaction_status' => 'settlement']);
                $this->line('200');

                return self::SUCCESS;
            }
            if ($this->option('operation') === 'close') {
                $request->merge(['cash_actual' => 0]);
                $response = app(ShiftController::class)->close($request);
            } else {
                $response = app(PosController::class)->store($request);
            }
            $this->line((string) $response->getStatusCode());
        } catch (ValidationException) {
            $this->line('422');
        } catch (\Throwable $exception) {
            report($exception);
            $this->error(get_class($exception));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
