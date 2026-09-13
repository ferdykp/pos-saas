<?php

namespace Tests\Feature\Pos;

use App\Models\Setting;

class InventorySafetyTest extends PosTestCase
{
    public function test_restock_updates_checkout_stock_and_physical_count_can_be_zero(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true, 'stock' => 0]);
        $this->shift($user);
        $this->actingAs($user)->post('/inventory/adjust', ['product_id' => $product->id, 'type' => 'stock_in', 'quantity' => 2, 'note' => 'Restok'])->assertRedirect();
        $this->postJson('/pos', $this->checkout($product))->assertOk();
        $this->assertEquals(1, $product->fresh()->stock);
        $this->post('/inventory/adjust', ['product_id' => $product->id, 'type' => 'adjustment', 'quantity' => 0, 'note' => 'Barang rusak'])->assertRedirect();
        $this->assertEquals(0, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'before_stock' => 1, 'after_stock' => 0, 'type' => 'adjustment']);
        $this->postJson('/pos', $this->checkout($product))->assertUnprocessable();
    }

    public function test_negative_stock_foreign_product_and_service_adjustment_are_rejected(): void
    {
        $user = $this->shop();
        $other = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $foreign = $this->product($other, ['manage_stock' => true]);
        $service = $this->product($user, ['type' => 'service']);
        $data = ['type' => 'stock_out', 'quantity' => 11, 'note' => 'Koreksi'];
        $this->actingAs($user)->postJson('/inventory/adjust', $data + ['product_id' => $product->id])->assertUnprocessable();
        $this->postJson('/inventory/adjust', $data + ['product_id' => $foreign->id])->assertNotFound();
        $this->postJson('/inventory/adjust', $data + ['product_id' => $service->id])->assertUnprocessable();
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_cashier_cannot_change_catalog_inventory_settings_or_forge_audit_rows(): void
    {
        $user = $this->shop('kasir');
        $product = $this->product($user);
        $this->actingAs($user);
        foreach (['/inventory/adjust', '/materials/update-stock', '/suppliers', '/categories', '/discounts', '/settings', '/settings/update-points'] as $url) {
            $this->postJson($url, [])->assertForbidden();
        }
        $this->deleteJson('/products/'.$product->id)->assertForbidden();
        $this->postJson('/stock-movements', [])->assertStatus(405);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_settings_ignore_unknown_keys_and_reject_invalid_tax(): void
    {
        $user = $this->shop();
        $this->actingAs($user)->post('/settings', ['tax_percentage' => 10, 'injected_config' => 'yes'])->assertRedirect();
        $this->assertFalse(Setting::where('key', 'injected_config')->exists());
        $this->postJson('/settings', ['tax_percentage' => -1])->assertUnprocessable();
        $this->assertEquals(10, Setting::where('key', 'tax_percentage')->value('value'));
    }
}
