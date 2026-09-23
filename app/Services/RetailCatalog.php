<?php

namespace App\Services;

use App\Models\Product;

class RetailCatalog
{
    /** The caller holds the tenant/product lock; old orders keep independent snapshots. */
    public function saveUnits(Product $product, array $units): void
    {
        $ids = [];
        foreach ($units as $data) {
            $unit = ! empty($data['id']) ? $product->units()->findOrFail($data['id']) : $product->units()->make();
            $unit->fill(['name' => $data['name'], 'factor' => $data['factor'], 'price' => $data['price']])->save();
            $ids[] = $unit->id;
        }
        $product->units()->whereNotIn('id', $ids)->delete();
    }
}
