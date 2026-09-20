<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;

class OrderStock
{
    /** Caller holds tenant and order locks; status transitions guarantee exactly one movement. */
    public function change(Order $order, bool $restore): void
    {
        foreach ($order->items()->orderBy('product_id')->orderBy('variant_id')->orderBy('id')->get() as $item) {
            if (! $item->reserved_stock) {
                continue;
            }
            $product = Product::withoutGlobalScopes()->where('tenant_id', $order->tenant_id)->whereKey($item->product_id)->lockForUpdate()->firstOrFail();
            $stock = $item->variant_id ? $product->variants()->whereKey($item->variant_id)->lockForUpdate()->firstOrFail() : $product;
            $before = (int) $stock->stock;
            if (! $restore && $before < $item->reserved_stock) {
                throw new \RuntimeException('Pembayaran terlambat membutuhkan rekonsiliasi stok untuk '.$order->invoice_number);
            }
            $stock->increment('stock', $restore ? $item->reserved_stock : -$item->reserved_stock);
            StockMovement::create([
                'tenant_id' => $order->tenant_id, 'product_id' => $item->product_id, 'user_id' => $order->user_id,
                'type' => $restore ? 'return' : 'sales', 'quantity' => $item->reserved_stock,
                'before_stock' => $before, 'after_stock' => $stock->stock,
                'reference_type' => 'order_item', 'reference_id' => $item->id,
                'note' => ($restore ? 'Pemulihan reservasi ' : 'Reservasi pembayaran terlambat ').$order->invoice_number,
            ]);
        }
        if ($restore) {
            app(RecipeStock::class)->restore($order);
        } else {
            app(RecipeStock::class)->deduct($order->tenant_id, app(RecipeStock::class)->quantities($order), $order);
        }
    }
}
