<?php

namespace Tests\Feature\Pos;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Setting;
use App\Services\MidtransGateway;

class CheckoutTest extends PosTestCase
{
    public function test_checkout_uses_database_prices_discount_tax_and_shift(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $shift = $this->shift($user);
        $discount = Discount::create(['tenant_id' => $user->tenant_id, 'name' => 'Promo', 'type' => 'percentage', 'value' => 10, 'is_active' => true]);
        $product->discounts()->attach($discount);
        foreach (['tax_active' => '1', 'tax_percentage' => '10'] as $key => $value) {
            Setting::create(['tenant_id' => $user->tenant_id, 'key' => $key, 'value' => $value]);
        }
        $data = $this->checkout($product, ['grand_total' => 9900, 'subtotal' => 1, 'discount' => 99999, 'tax' => 0, 'items' => [['id' => $product->id, 'quantity' => 1, 'price' => 1, 'name' => 'Fake']]]);
        $this->actingAs($user)->postJson('/pos', $data)->assertOk()->assertJsonPath('grand_total', 9900);
        $order = Order::firstOrFail();
        $this->assertEquals(10000, $order->subtotal);
        $this->assertEquals(1000, $order->discount);
        $this->assertEquals(900, $order->tax);
        $this->assertEquals($shift->id, $order->shift_id);
        $this->assertEquals('Kopi', $order->items->first()->product_name);
        $this->assertEquals(9, $product->fresh()->stock);
    }

    public function test_negative_quantity_and_fake_total_are_rejected_without_writes(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['items' => [['id' => $product->id, 'quantity' => -1]]]))->assertUnprocessable();
        $this->postJson('/pos', $this->checkout($product, ['grand_total' => 1]))->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_foreign_product_and_customer_are_rejected(): void
    {
        $user = $this->shop();
        $other = $this->shop();
        $foreign = $this->product($other);
        $local = $this->product($user);
        $this->shift($user);
        $customer = Customer::create(['tenant_id' => $other->tenant_id, 'name' => 'Other']);
        $this->actingAs($user)->postJson('/pos', $this->checkout($foreign))->assertUnprocessable();
        $this->postJson('/pos', $this->checkout($local, ['customer_id' => $customer->id]))->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_no_shift_or_insufficient_stock_prevents_checkout(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true, 'stock' => 0]);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertUnprocessable();
        $this->shift($user);
        $this->postJson('/pos', $this->checkout($product))->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_untracked_products_do_not_reduce_stock(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => false]);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertOk();
        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_gateway_failure_preserves_reconciliation_invoice_and_releases_stock(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $this->mock(MidtransGateway::class, fn ($mock) => $mock->shouldReceive('charge')->once()->andThrow(new \RuntimeException('Gateway failed')));
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['payment_method' => 'midtrans']))->assertStatus(503);
        $this->assertDatabaseHas('orders', ['payment_status' => 'unpaid', 'order_status' => 'cancelled']);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_pos_page_renders_with_current_catalog(): void
    {
        $user = $this->shop();
        $this->product($user, ['manage_stock' => true]);
        $this->actingAs($user)->get('/pos')->assertOk()->assertSee('Kopi');
    }
}
