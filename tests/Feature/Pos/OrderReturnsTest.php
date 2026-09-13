<?php

namespace Tests\Feature\Pos;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderReturnsTest extends PosTestCase
{
    public function test_partial_return_retries_then_full_return_restore_stock_and_cash_once(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['items' => [['id' => $product->id, 'quantity' => 2]], 'grand_total' => 20000, 'paid_amount' => 20000]))->assertOk();
        $order = Order::firstOrFail();
        $data = ['operation_key' => (string) Str::uuid(), 'item_id' => $order->items->first()->id, 'quantity' => 1, 'restock' => 1, 'reason' => 'Salah beli'];
        $this->post('/orders/'.$order->id.'/returns', $data)->assertRedirect();
        $this->post('/orders/'.$order->id.'/returns', $data)->assertRedirect();
        $this->assertEquals(9, $product->fresh()->stock);
        $this->getJson('/shifts/summary')->assertJsonPath('cash_expected', 110000);
        $data['operation_key'] = (string) Str::uuid();
        $this->post('/orders/'.$order->id.'/returns', $data)->assertRedirect();
        $this->assertEquals(10, $product->fresh()->stock);
        $this->getJson('/shifts/summary')->assertJsonPath('cash_expected', 100000);
        $data['operation_key'] = (string) Str::uuid();
        $this->postJson('/orders/'.$order->id.'/returns', $data)->assertUnprocessable();
        $this->assertDatabaseCount('order_returns', 2);
        $this->assertEquals(20000, $order->fresh()->grand_total);
    }
}
