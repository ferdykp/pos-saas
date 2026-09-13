<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Validation\ValidationException;

class RecipeStock
{
    // Called inside the checkout/settlement transaction; stored quantities are immutable snapshots.
    public function deduct(int $tenantId, array $quantities, ?Order $order = null): void
    {
        ksort($quantities);
        foreach ($quantities as $id => $quantity) {
            $material = Material::withoutGlobalScopes()->where('tenant_id', $tenantId)->lockForUpdate()->find($id);
            if (! $material || $material->stock < $quantity) {
                throw ValidationException::withMessages(['items' => 'Bahan baku '.($material?->name ?? 'menu').' tidak mencukupi.']);
            }
            $before = $material->stock;
            $material->decrement('stock', $quantity);
            if ($order) {
                $this->record($order, $material, $quantity, $before, 'sales');
            }
        }
    }

    public function quantities(Order $order): array
    {
        $quantities = [];
        foreach ($order->items as $item) {
            foreach ($item->reserved_materials ?? [] as $id => $qty) {
                $quantities[$id] = ($quantities[$id] ?? 0) + $qty;
            }
        }

        return $quantities;
    }

    public function restore(Order $order): void
    {
        $quantities = $this->quantities($order);
        ksort($quantities);
        foreach ($quantities as $id => $qty) {
            $material = Material::withoutGlobalScopes()->where('tenant_id', $order->tenant_id)->whereKey($id)->lockForUpdate()->firstOrFail();
            $before = $material->stock;
            $material->increment('stock', $qty);
            $this->record($order, $material, $qty, $before, 'return');
        }
    }

    private function record(Order $order, Material $material, int $quantity, int $before, string $type): void
    {
        StockMovement::create(['tenant_id' => $order->tenant_id, 'material_id' => $material->id, 'user_id' => $order->user_id, 'type' => $type, 'quantity' => $quantity, 'before_stock' => $before, 'after_stock' => $material->stock, 'note' => 'Reservasi / pemulihan bahan '.$order->invoice_number, 'reference_type' => 'order', 'reference_id' => $order->id]);
    }
}
