<?php

namespace Tests\Feature\Pos;

use App\Models\Material;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CafeWorkflowTest extends PosTestCase
{
    public function test_retrying_checkout_does_not_duplicate_order_or_stock(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $payload = $this->checkout($product, ['checkout_key' => (string) Str::uuid()]);
        $this->actingAs($user)->postJson('/pos', $payload)->assertOk();
        $this->postJson('/pos', $payload)->assertOk();
        $this->assertDatabaseCount('orders', 1);
        $this->assertEquals(9, $product->fresh()->stock);
        $payload['paid_amount'] = 20000;
        $this->postJson('/pos', $payload)->assertStatus(409);
    }

    public function test_only_own_pending_qr_payments_are_recovered(): void
    {
        $user = $this->shop();
        $other = $this->shop();
        $product = $this->product($user);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertOk();
        $order = Order::firstOrFail();
        $order->update(['payment_method' => 'midtrans', 'payment_status' => 'unpaid', 'order_status' => 'pending', 'qr_url' => 'https://example.test/qr']);
        $this->get('/pos')->assertViewHas('pendingPayments', fn ($payments) => $payments->pluck('order_id')->all() === [$order->id]);
        $this->actingAs($other)->get('/pos')->assertViewHas('pendingPayments', fn ($payments) => $payments->isEmpty());
        $order->update(['order_status' => 'cancelled']);
        $this->actingAs($user)->get('/pos')->assertViewHas('pendingPayments', fn ($payments) => $payments->isEmpty());
    }

    public function test_variant_addon_recipe_and_kitchen_work_together(): void
    {
        $user = $this->shop();
        $user->tenant->update(['business_type' => 'food']);
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $variant = $product->variants()->create(['name' => 'Large', 'sku' => 'LARGE', 'price' => 15000, 'stock' => 5]);
        $addon = $product->addons()->create(['name' => 'Extra shot', 'price' => 3000, 'cost' => 1000]);
        $material = Material::create(['tenant_id' => $user->tenant_id, 'name' => 'Susu', 'sku' => 'MILK', 'unit' => 'ml', 'stock' => 1000]);
        $product->materials()->attach($material->id, ['quantity' => 200]);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['grand_total' => 18000, 'paid_amount' => 20000, 'items' => [['id' => $product->id, 'variant_id' => $variant->id, 'addon_ids' => [$addon->id], 'quantity' => 1, 'note' => 'Tanpa gula']]]))->assertOk();
        $order = Order::firstOrFail();
        $this->assertEquals(800, $material->fresh()->stock);
        $this->assertEquals(4, $variant->fresh()->stock);
        $this->assertEquals(10, $product->fresh()->stock);
        $this->get('/kitchen')->assertOk()->assertSee('Extra shot')->assertSee('Tanpa gula');
        $this->patch('/kitchen/'.$order->id, ['status' => 'served'])->assertStatus(409);
        foreach (['preparing', 'ready', 'served'] as $status) {
            $this->patch('/kitchen/'.$order->id, ['status' => $status])->assertRedirect();
        }
        $this->assertSame('served', $order->fresh()->kitchen_status);
    }

    public function test_combined_recipe_demand_rolls_back_all_items_when_insufficient(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['manage_stock' => true]);
        $this->shift($user);
        $material = Material::create(['tenant_id' => $user->tenant_id, 'name' => 'Susu', 'sku' => 'MILK', 'unit' => 'ml', 'stock' => 300]);
        $product->materials()->attach($material->id, ['quantity' => 200]);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['grand_total' => 20000, 'paid_amount' => 20000, 'items' => [['id' => $product->id, 'quantity' => 1, 'note' => 'Less ice'], ['id' => $product->id, 'quantity' => 1, 'note' => 'Normal']]]))->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(300, $material->fresh()->stock);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_foreign_addon_variant_recipe_and_kitchen_order_are_rejected(): void
    {
        $user = $this->shop();
        $other = $this->shop();
        $local = $this->product($user);
        $foreign = $this->product($other);
        $this->shift($user);
        $variant = $foreign->variants()->create(['name' => 'Large', 'sku' => 'OTHER', 'price' => 15000, 'stock' => 5]);
        $addon = $foreign->addons()->create(['name' => 'Other', 'price' => 1]);
        $this->actingAs($user)->postJson('/pos', $this->checkout($local, ['items' => [['id' => $local->id, 'quantity' => 1, 'variant_id' => $variant->id]]]))->assertUnprocessable();
        $this->postJson('/pos', $this->checkout($local, ['items' => [['id' => $local->id, 'quantity' => 1, 'addon_ids' => [$addon->id]]]]))->assertUnprocessable();
        $this->postJson('/menu/'.$foreign->id.'/configure', ['kind' => 'addon', 'name' => 'X', 'price' => 1])->assertNotFound();
    }

    public function test_dashboard_uses_selected_period_and_cost_snapshots(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['cost_price' => 4000]);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertOk();
        $this->get('/dashboard')->assertOk()->assertViewHas('revenue', 10000)->assertViewHas('grossProfit', 6000);
        $product->update(['cost_price' => 9000]);
        $this->get('/dashboard')->assertViewHas('grossProfit', 6000);
        $this->get('/dashboard?start_date=2020-01-01&end_date=2020-01-01')->assertOk()->assertViewHas('revenue', 0);
        $this->get('/dashboard?start_date=invalid')->assertSessionHasErrors('start_date');
    }

    public function test_cashier_is_sent_to_terminal_and_cannot_configure_menu(): void
    {
        $user = $this->shop('kasir');
        $this->actingAs($user)->get('/dashboard')->assertRedirect('/pos');
        $this->get('/menu/configure')->assertRedirect('/pos');
    }

    public function test_import_is_atomic_and_sample_is_not_duplicated(): void
    {
        $user = $this->shop();
        $this->actingAs($user);
        $file = UploadedFile::fake()->createWithContent('menu.csv', "name,category,price,cost,stock,manage_stock\nKopi,Kopi,15000,6000,10,1\nTeh,Minuman,-1,0,0,0\n");
        $this->post('/getting-started/import', ['file' => $file])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('products', 0);
        $file = UploadedFile::fake()->createWithContent('menu.csv', "name,category,price,cost,stock,manage_stock\nKopi,Kopi,15000,6000,10,1\n");
        $this->post('/getting-started/import', ['file' => $file])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('products', 1);
        $this->post('/getting-started/sample')->assertSessionHasErrors('menu');
        $this->get('/getting-started')->assertOk();
        $this->get('/menu/configure')->assertOk();
        $this->get('/help')->assertOk();
    }

    public function test_browser_utc_timestamp_matches_local_shift_time(): void
    {
        $user = $this->shop();
        $product = $this->product($user);
        $shift = $this->shift($user);
        $payload = $this->checkout($product, ['shift_id' => $shift->id, 'sold_at' => now()->utc()->toISOString()]);
        $this->actingAs($user)->postJson('/pos', $payload)->assertOk();
        $this->assertTrue(Order::first()->sold_at->greaterThanOrEqualTo(Carbon::parse($shift->start_time, config('app.timezone'))));
    }
}
