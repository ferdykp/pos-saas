<?php

namespace Tests\Feature\Pos;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Str;

class CashOperationsTest extends PosTestCase
{
    public function test_deposit_retries_and_settlement_reconcile_cash_and_customer_debt(): void
    {
        $user = $this->shop();
        $product = $this->product($user);
        $this->shift($user);
        $customer = Customer::create(['tenant_id' => $user->tenant_id, 'name' => 'Pelanggan']);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['payment_status' => 'unpaid', 'customer_id' => $customer->id]))->assertOk();
        $order = Order::firstOrFail();
        $deposit = ['operation_key' => (string) Str::uuid(), 'amount' => 3000, 'reason' => 'DP'];
        $this->post('/orders/'.$order->id.'/payments', $deposit)->assertRedirect();
        $this->post('/orders/'.$order->id.'/payments', $deposit)->assertRedirect();
        $this->assertEquals(7000, $customer->fresh()->total_debt);
        $this->assertEquals('unpaid', $order->fresh()->payment_status);
        $this->getJson('/shifts/summary')->assertJsonPath('cash_expected', 103000);
        $this->postJson('/orders/'.$order->id.'/payments', array_replace($deposit, ['amount' => 4000]))->assertUnprocessable();
        $this->post('/orders/'.$order->id.'/payments', ['operation_key' => (string) Str::uuid(), 'amount' => 7000, 'reason' => 'Lunas'])->assertRedirect();
        $this->assertEquals(0, $customer->fresh()->total_debt);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->getJson('/shifts/summary')->assertJsonPath('cash_expected', 110000);
        $this->assertDatabaseCount('payments', 2);
    }

    public function test_expense_is_idempotent_and_cannot_exceed_cash(): void
    {
        $user = $this->shop();
        $this->shift($user);
        $data = ['operation_key' => (string) Str::uuid(), 'amount' => 10000, 'reason' => 'Beli kantong'];
        $this->actingAs($user)->post('/cash/expenses', $data)->assertRedirect();
        $this->post('/cash/expenses', $data)->assertRedirect();
        $this->getJson('/shifts/summary')->assertJsonPath('cash_expected', 90000);
        $this->postJson('/cash/expenses', array_replace($data, ['operation_key' => (string) Str::uuid(), 'amount' => 100000]))->assertUnprocessable();
        $this->assertDatabaseCount('cash_entries', 1);
        $this->assertDatabaseCount('activity_logs', 1);
    }

    public function test_cancel_unpaid_order_restores_stock_and_debt_once(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $customer = Customer::create(['tenant_id' => $user->tenant_id, 'name' => 'Pelanggan']);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['payment_status' => 'unpaid', 'customer_id' => $customer->id]))->assertOk();
        $order = Order::firstOrFail();
        $this->post('/orders/'.$order->id.'/cancel', ['reason' => 'Pelanggan batal'])->assertRedirect();
        $this->post('/orders/'.$order->id.'/cancel', ['reason' => 'Pelanggan batal'])->assertRedirect();
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertEquals(0, $customer->fresh()->total_debt);
        $this->assertEquals('cancelled', $order->fresh()->order_status);
        $this->assertDatabaseCount('activity_logs', 1);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'return',
            'before_stock' => 9,
            'after_stock' => 10,
            'reference_type' => 'order_item',
        ]);
    }
}
