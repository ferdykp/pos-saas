<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\MidtransGateway;
use App\Services\OrderPaymentService;
use Illuminate\Console\Command;

class ReconcilePayments extends Command
{
    protected $signature = 'growpos:reconcile-payments {--limit=100}';

    protected $description = 'Poll existing QRIS references; never create new charges';

    public function handle(MidtransGateway $gateway, OrderPaymentService $payments): int
    {
        $limit = max(1, min(500, (int) $this->option('limit')));
        $orders = Order::withoutGlobalScopes()->where('payment_method', 'midtrans')->where('payment_status', 'unpaid')
            ->where(fn ($query) => $query->where('order_status', 'pending')->orWhere('payment_attention', true))
            ->where('created_at', '<', now()->subMinute())->orderBy('updated_at')->limit($limit)->get();
        $errors = 0;
        foreach ($orders as $order) {
            try {
                $payments->apply($order->id, $gateway->status($order->invoice_number));
                Order::withoutGlobalScopes()->whereKey($order->id)->update(['updated_at' => now()]);
            } catch (\Throwable $exception) {
                $errors++;
                // Conditional update cannot overwrite a concurrent successful settlement.
                Order::withoutGlobalScopes()->whereKey($order->id)->where('payment_status', 'unpaid')->update(['payment_attention' => true, 'updated_at' => now()]);
                report($exception);
            }
        }
        $this->line('Checked '.$orders->count().' existing references; '.$errors.' require another check.');

        return $errors ? self::FAILURE : self::SUCCESS;
    }
}
