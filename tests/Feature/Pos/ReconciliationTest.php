<?php

namespace Tests\Feature\Pos;

use App\Models\Order;
use App\Services\MidtransGateway;

class ReconciliationTest extends PosTestCase
{
    public function test_background_check_recovers_paid_reference_without_creating_charge(): void
    {
        $user = $this->shop();
        $product = $this->product($user);
        $this->shift($user);
        $this->mock(MidtransGateway::class, fn ($mock) => $mock->shouldReceive('charge')->once()->andThrow(new \RuntimeException('timeout')));
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['payment_method' => 'midtrans']))->assertStatus(503);
        $order = Order::firstOrFail();
        $order->forceFill(['created_at' => now()->subMinutes(2)])->save();
        $this->assertTrue((bool) $order->fresh()->payment_attention);
        $this->mock(MidtransGateway::class, function ($mock) use ($order) {
            $mock->shouldNotReceive('charge');
            $mock->shouldReceive('status')->once()->with($order->invoice_number)->andReturn(['order_id' => $order->invoice_number, 'gross_amount' => $order->grand_total, 'transaction_status' => 'settlement']);
        });
        $this->artisan('growpos:reconcile-payments')->assertSuccessful();
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertFalse((bool) $order->fresh()->payment_attention);
    }

    public function test_cashier_cannot_access_owner_review_queue(): void
    {
        $user = $this->shop('kasir');
        $this->actingAs($user)->getJson('/payments/review')->assertForbidden();
    }
}
