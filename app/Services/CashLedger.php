<?php

namespace App\Services;

use App\Models\CashEntry;
use App\Models\Order;
use App\Models\Shift;
use Illuminate\Support\Str;

class CashLedger
{
    public function net(Shift $shift): float
    {
        $legacy = Order::withoutGlobalScopes()->where('tenant_id', $shift->tenant_id)
            ->where('shift_id', $shift->id)->where('cash_tracked', false)
            ->where('payment_method', 'cash')->where('payment_status', 'paid')
            ->where('order_status', 'completed')->sum('grand_total');
        $entries = CashEntry::withoutGlobalScopes()->where('tenant_id', $shift->tenant_id)->where('shift_id', $shift->id)->sum('amount');

        return (float) $legacy + (float) $entries;
    }

    public function checkout(Order $order): void
    {
        $order->update(['cash_tracked' => true]);
        if ($order->payment_method === 'cash' && $order->payment_status === 'paid' && $order->grand_total > 0) {
            CashEntry::create([
                'tenant_id' => $order->tenant_id, 'shift_id' => $order->shift_id, 'user_id' => $order->user_id,
                'order_id' => $order->id, 'operation_key' => (string) Str::uuid(), 'kind' => 'sale',
                'amount' => $order->grand_total, 'reason' => 'Pembayaran '.$order->invoice_number,
            ]);
        }
    }
}
