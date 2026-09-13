<?php

namespace App\Services;

use App\Models\Product;

class OrderPricing
{
    // POS uses whole rupiah. Round once per unit and once for tax.
    public function unitPrice(Product $product, ?float $override = null): array
    {
        $price = (int) round($override ?? $product->sell_price);
        $discount = $product->discounts->sortBy('id')->first(fn ($discount) => $discount->isValidNow());
        $amount = $discount ? ($discount->type === 'percentage' ? $price * $discount->value / 100 : $discount->value) : 0;
        $amount = min($price, max(0, (int) round($amount)));

        return ['price' => $price, 'discount' => $amount, 'final' => $price - $amount, 'discount_name' => $discount?->name];
    }
}
