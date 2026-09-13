<?php

namespace Tests\Feature\Pos;

use App\Models\Order;

class OperationalDetailsTest extends PosTestCase
{
    public function test_mixed_checkout_sends_only_prepared_items_to_kitchen(): void
    {
        $user = $this->shop();
        $user->tenant->update(['business_type' => 'mixed', 'business_modules' => ['goods', 'food']]);
        $food = $this->product($user, ['product_name' => 'Makanan dimasak', 'requires_preparation' => true]);
        $goods = $this->product($user, ['product_name' => 'Sabun retail', 'requires_preparation' => false]);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($food, ['grand_total' => 20000, 'paid_amount' => 20000, 'items' => [['id' => $food->id, 'quantity' => 1], ['id' => $goods->id, 'quantity' => 1]]]))->assertOk();
        $this->get('/kitchen')->assertOk()->assertSee('Makanan dimasak')->assertDontSee('Sabun retail');
    }

    public function test_service_estimate_persists_and_overdue_work_is_visible(): void
    {
        $user = $this->shop();
        $service = $this->product($user, ['type' => 'service']);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($service))->assertOk();
        $order = Order::firstOrFail();
        $this->patch('/services/'.$order->id, ['status' => 'working', 'service_due_at' => now()->subHour()->format('Y-m-d\TH:i')])->assertRedirect();
        $this->assertNotNull($order->fresh()->service_due_at);
        $this->get('/services?status=working')->assertOk()->assertSee('Melewati estimasi selesai');
    }
}
