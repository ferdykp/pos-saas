<?php

namespace Tests\Feature\Pos;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Setting;
use App\Services\CustomerPoints;
use App\Services\MidtransGateway;
use App\Services\OrderPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentsTest extends PosTestCase
{
    private function pendingOrder(): array
    {
        config(['services.midtrans.server_key' => 'test-key']);
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $this->mock(MidtransGateway::class, fn ($mock) => $mock->shouldReceive('charge')->andReturn('https://example.test/qr'));
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['payment_method' => 'midtrans']))->assertOk();

        return [$user, $product, Order::firstOrFail()];
    }

    private function payload(Order $order, string $status = 'settlement', string $amount = '10000.00'): array
    {
        return ['order_id' => $order->invoice_number, 'transaction_status' => $status, 'status_code' => '200', 'gross_amount' => $amount, 'signature_key' => hash('sha512', $order->invoice_number.'200'.$amount.'test-key')];
    }

    public function test_webhook_then_polling_credits_new_wallet_once(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $this->postJson('/api/midtrans/callback', $this->payload($order))->assertOk();
        $this->postJson('/midtrans/callback', $this->payload($order))->assertOk();
        $this->getJson('/orders/'.$order->id.'/check-status')->assertOk()->assertJsonPath('status', 'paid');
        $this->assertEquals(9850, DB::table('tenant_wallets')->value('balance'));
        $this->assertEquals(10000, $order->fresh()->paid_amount);
        $this->assertSame('pending', $order->fresh()->withdrawal_status);
        $this->assertEquals(9, $product->fresh()->stock);
    }

    public function test_polling_then_webhook_credits_once(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $payload = $this->payload($order);
        $this->mock(MidtransGateway::class, fn ($mock) => $mock->shouldReceive('status')->once()->andReturn($payload));
        $this->getJson('/orders/'.$order->id.'/check-status')->assertOk()->assertJsonPath('status', 'paid');
        $this->postJson('/api/midtrans/callback', $payload)->assertOk();
        $this->assertEquals(9850, DB::table('tenant_wallets')->value('balance'));
    }

    public function test_repeated_expiry_restores_stock_once_and_never_reverses_paid_order(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $this->postJson('/api/midtrans/callback', $this->payload($order, 'expire'))->assertOk();
        $this->postJson('/api/midtrans/callback', $this->payload($order, 'expire'))->assertOk();
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'return',
            'before_stock' => 9,
            'after_stock' => 10,
            'reference_type' => 'order_item',
        ]);
        $this->postJson('/api/midtrans/callback', $this->payload($order))->assertOk();
        $this->postJson('/api/midtrans/callback', $this->payload($order, 'expire'))->assertOk();
        $this->assertEquals(9, $product->fresh()->stock);
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertDatabaseCount('stock_movements', 3);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'sales',
            'before_stock' => 10,
            'after_stock' => 9,
            'reference_type' => 'order_item',
        ]);
    }

    public function test_gateway_charge_failure_releases_reserved_stock_without_holding_a_pending_order(): void
    {
        config(['services.midtrans.server_key' => 'test-key']);
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $payload = $this->checkout($product, ['payment_method' => 'midtrans', 'checkout_key' => (string) Str::uuid()]);
        $this->mock(MidtransGateway::class, fn ($mock) => $mock->shouldReceive('charge')->once()->andThrow(new \RuntimeException('gateway timeout')));

        $this->actingAs($user)->postJson('/pos', $payload)->assertStatus(503)->assertJsonPath('success', false)->assertDontSee('gateway timeout');

        $order = Order::firstOrFail();
        $this->assertSame('cancelled', $order->order_status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertNull($order->qr_url);
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertDatabaseCount('stock_movements', 2);

        // A browser retry with the same checkout key must not create or charge a second order.
        $this->actingAs($user)->postJson('/pos', $payload)->assertOk()->assertJsonPath('order_status', 'cancelled');
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_gateway_runs_after_order_commit_and_concurrent_settlement_survives_timeout(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $baselineLevel = DB::transactionLevel();
        $this->mock(MidtransGateway::class, function ($mock) use ($baselineLevel) {
            $mock->shouldReceive('charge')->once()->andReturnUsing(function ($invoice, $amount) use ($baselineLevel) {
                $this->assertSame($baselineLevel, DB::transactionLevel());
                $order = Order::where('invoice_number', $invoice)->firstOrFail();
                app(OrderPaymentService::class)->apply($order->id, ['order_id' => $invoice, 'gross_amount' => $amount, 'transaction_status' => 'settlement']);
                throw new \RuntimeException('Simulated response timeout after settlement');
            });
        });
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['payment_method' => 'midtrans']))->assertOk()->assertJsonPath('payment_status', 'paid');
        $this->assertEquals(9, $product->fresh()->stock);
        $this->assertEquals(9850, DB::table('tenant_wallets')->value('balance'));
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_qris_requires_stable_reference_before_any_gateway_call(): void
    {
        $user = $this->shop();
        $product = $this->product($user);
        $this->shift($user);
        $this->mock(MidtransGateway::class, fn ($mock) => $mock->shouldNotReceive('charge'));
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['payment_method' => 'midtrans', 'checkout_key' => null]))->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_invalid_signature_and_mismatched_amount_do_not_credit(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $payload = $this->payload($order);
        $payload['signature_key'] = 'invalid';
        $this->postJson('/api/midtrans/callback', $payload)->assertForbidden();
        $this->postJson('/api/midtrans/callback', $this->payload($order, 'settlement', '1.00'))->assertUnprocessable();
        $this->assertDatabaseCount('tenant_wallets', 0);
        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }

    public function test_internal_failure_is_retryable_and_rolls_back_wallet_and_order(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $this->mock(CustomerPoints::class, fn ($mock) => $mock->shouldReceive('award')->once()->andThrow(new \RuntimeException('Internal secret')));
        $this->postJson('/api/midtrans/callback', $this->payload($order))->assertStatus(503)->assertDontSee('Internal secret');
        $this->assertDatabaseCount('tenant_wallets', 0);
        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }

    public function test_cashier_cannot_poll_another_tenants_order(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $other = $this->shop();
        $this->actingAs($other)->getJson('/orders/'.$order->id.'/check-status')->assertNotFound();
    }

    public function test_points_are_awarded_only_once_for_duplicate_settlement(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $customer = Customer::create(['tenant_id' => $user->tenant_id, 'name' => 'Member', 'is_member' => true]);
        $order->update(['customer_id' => $customer->id]);
        foreach (['point_mode' => 'flat', 'point_rule_value' => '5'] as $key => $value) {
            Setting::create(['tenant_id' => $user->tenant_id, 'key' => $key, 'value' => $value]);
        }
        $this->postJson('/api/midtrans/callback', $this->payload($order))->assertOk();
        $this->postJson('/api/midtrans/callback', $this->payload($order))->assertOk();
        $this->assertEquals(5, $customer->fresh()->points);
    }

    public function test_capture_with_fraud_challenge_does_not_credit(): void
    {
        [$user, $product, $order] = $this->pendingOrder();
        $payload = $this->payload($order, 'capture');
        $payload['fraud_status'] = 'challenge';
        $this->postJson('/api/midtrans/callback', $payload)->assertOk();
        $this->assertDatabaseCount('tenant_wallets', 0);
        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }
}
