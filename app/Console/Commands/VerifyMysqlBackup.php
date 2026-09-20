<?php

namespace App\Console\Commands;

use App\Services\PrivateMysqlClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyMysqlBackup extends Command
{
    protected $signature = 'growpos:verify-mysql-backup {file : Backup created by growpos:backup}';

    protected $description = 'Restore a trusted private backup into a random temporary database and check financial invariants';

    public function handle(PrivateMysqlClient $client): int
    {
        $path = realpath($this->argument('file'));
        $root = realpath(storage_path('app/private/backups'));
        if (! $path || ! $root || ! str_starts_with($path, $root.DIRECTORY_SEPARATOR) || ! str_ends_with($path, '.sql') || ! is_file($path.'.sha256') || ! hash_equals(trim(file_get_contents($path.'.sha256')), hash_file('sha256', $path))) {
            $this->error('Only checksum-verified backups from the private backup directory are accepted.');

            return self::FAILURE;
        }
        // Refuse commands that could redirect restoration outside the generated database.
        $handle = fopen($path, 'rb');
        while (($line = fgets($handle)) !== false) {
            if (preg_match('/^\s*(USE\s|(?:CREATE|DROP|ALTER)\s+DATABASE\s|DELIMITER\s)/i', $line)) {
                fclose($handle);
                $this->error('Backup contains database switching or stored-program commands; use a separate restore server.');

                return self::FAILURE;
            }
        }
        fclose($handle);
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'mysql') {
            $this->error('This verification requires MySQL.');

            return self::FAILURE;
        }
        $database = 'growpos_verify_'.bin2hex(random_bytes(12));
        $created = false;
        try {
            $connection->statement('CREATE DATABASE `'.$database.'`');
            $created = true;
            $client->restore($connection->getConfig(), $database, $path);
            config(['database.connections.restore_verify' => array_replace($connection->getConfig(), ['name' => 'restore_verify', 'database' => $database, 'url' => null])]);
            $restored = DB::connection('restore_verify');
            foreach (['tenants', 'orders', 'order_items', 'products', 'cash_entries', 'order_returns', 'tenant_wallets'] as $table) {
                $restored->table($table)->count();
                $this->line('PASS restored table '.$table);
            }
            $badProducts = $restored->table('products')->where('stock', '<', 0)->count();
            $badItems = $restored->table('order_items')->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')->whereNull('orders.id')->count();
            if ($badProducts || $badItems) {
                $this->error('Backup restored, but negative stock or orphaned order items require reconciliation.');

                return self::FAILURE;
            }
            $this->info('PASS isolated restore, nonnegative product stock, order-item references.');

            return self::SUCCESS;
        } catch (\Throwable) {
            $this->error('Restore verification failed. Source database was not modified.');

            return self::FAILURE;
        } finally {
            DB::purge('restore_verify');
            if ($created) {
                $connection->statement('DROP DATABASE `'.$database.'`');
            }
        }
    }
}
