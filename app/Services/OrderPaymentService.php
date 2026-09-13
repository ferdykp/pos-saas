<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderPaymentService
{
    public function apply(int $orderId, array $notification): void
    {
        DB::transaction(function () use ($orderId, $notification) {
            $order = Order::withoutGlobalScopes()->lockForUpdate()->findOrFail($orderId);
            if ($order->payment_method !== 'midtrans' || ($notification['order_id'] ?? null) !== $order->invoice_number || ! is_numeric($notification['gross_amount'] ?? null) || round((float) $notification['gross_amount'], 2) !== round((float) $order->grand_total, 2)) {
                throw ValidationException::withMessages(['payment' => 'Identitas atau nominal pembayaran tidak sesuai.']);
            }
            $status = $notification['transaction_status'] ?? '';
            $settled = $status === 'settlement' || ($status === 'capture' && ($notification['fraud_status'] ?? '') === 'accept');
            if ($order->payment_status === 'paid') {
                return;
            }

            if ($settled) {
                // A paid notification can arrive after expiry. Reacquire only stock actually released.
                if ($order->order_status === 'cancelled') {
                    foreach ($order->items()->orderBy('product_id')->get() as $item) {
                        if (! $item->reserved_stock) {
                            continue;
                        }
                        $product = Product::withoutGlobalScopes()->where('tenant_id', $order->tenant_id)->lockForUpdate()->findOrFail($item->product_id);
                        $product = $item->variant_id ? $product->variants()->lockForUpdate()->findOrFail($item->variant_id) : $product;
                        if ($product->stock < $item->reserved_stock) {
                            throw new \RuntimeException('Pembayaran terlambat membutuhkan rekonsiliasi stok untuk '.$order->invoice_number);
                        }
                        $product->decrement('stock', $item->reserved_stock);
                    }
                }
                if ($order->order_status === 'cancelled') {
                    app(RecipeStock::class)->deduct($order->tenant_id, app(RecipeStock::class)->quantities($order), $order);
                }
                // Whole-rupiah wallet: round the fee consistently in every settlement path.
                $fee = (int) round($order->grand_total * config('platform.commission_rate', 0.015));
                $net = (int) round($order->grand_total) - $fee;
                DB::table('tenant_wallets')->insertOrIgnore(['tenant_id' => $order->tenant_id, 'balance' => 0, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('tenant_wallets')->where('tenant_id', $order->tenant_id)->increment('balance', $net, ['updated_at' => now()]);
                $order->update(['payment_status' => 'paid', 'order_status' => 'completed', 'paid_amount' => $order->grand_total, 'withdrawal_status' => 'pending']);
                app(CustomerPoints::class)->award($order);
            } elseif (in_array($status, ['cancel', 'deny', 'expire'], true) && $order->order_status !== 'cancelled') {
                foreach ($order->items()->orderBy('product_id')->get() as $item) {
                    if ($item->reserved_stock) {
                        $product = Product::withoutGlobalScopes()->where('tenant_id', $order->tenant_id)->findOrFail($item->product_id);
                        ($item->variant_id ? $product->variants()->findOrFail($item->variant_id) : $product)->increment('stock', $item->reserved_stock);
                    }
                }
                app(RecipeStock::class)->restore($order);
                $order->update(['order_status' => 'cancelled']);
            }
        }, 3);
    }
}
