<?php

namespace Tests\Feature\Pos;

use App\Models\Order;
use App\Models\Product;
use App\Support\BusinessProfile;
use Illuminate\Http\UploadedFile;

class BusinessProfileTest extends PosTestCase
{
    public function test_legacy_types_have_compatible_defaults(): void
    {
        $this->assertSame('food', BusinessProfile::normalize('F&B / Resto / Cafe'));
        $this->assertSame('service', BusinessProfile::normalize('Jasa / Service'));
        $this->assertSame('grocery', BusinessProfile::normalize('Minimarket / Sembako'));
        $this->assertSame(['goods', 'services'], BusinessProfile::defaults('mixed'));
    }

    public function test_admin_can_change_own_profile_without_changing_catalog(): void
    {
        $user = $this->shop();
        $other = $this->shop();
        $product = $this->product($user);
        $this->actingAs($user)->put('/business', ['business_type' => 'mixed', 'business_modules' => ['goods', 'services', 'food'], 'tenant_id' => $other->tenant_id])->assertRedirect();
        $this->assertSame('mixed', $user->tenant->fresh()->business_type);
        $this->assertSame('retail', $other->tenant->fresh()->business_type);
        $this->assertSame($product->product_name, $product->fresh()->product_name);
        $this->putJson('/business', ['business_type' => 'unknown', 'business_modules' => []])->assertUnprocessable();
        $this->get('/business')->assertOk();
    }

    public function test_cashier_cannot_change_business_profile(): void
    {
        $this->actingAs($this->shop('kasir'))->put('/business', ['business_type' => 'food', 'business_modules' => ['food']])->assertRedirect('/pos');
    }

    public function test_mixed_sale_only_deducts_goods_and_does_not_create_kitchen_order(): void
    {
        $user = $this->shop();
        $user->tenant->update(['business_type' => 'mixed']);
        $goods = $this->product($user, ['manage_stock' => true]);
        $service = $this->product($user, ['type' => 'service', 'manage_stock' => true, 'stock' => 0]);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($goods, ['grand_total' => 20000, 'paid_amount' => 20000, 'items' => [['id' => $goods->id, 'quantity' => 1], ['id' => $service->id, 'quantity' => 1]]]))->assertOk();
        $this->assertEquals(9, $goods->fresh()->stock);
        $this->assertEquals(0, $service->fresh()->stock);
        $this->assertNull(Order::firstOrFail()->kitchen_status);
        $this->assertDatabaseCount('order_items', 2);
    }

    public function test_legacy_service_api_cannot_link_other_tenant_or_reassign_order(): void
    {
        $user = $this->shop();
        $other = $this->shop();
        $product = $this->product($other);
        $this->shift($other);
        $this->actingAs($other)->postJson('/pos', $this->checkout($product))->assertOk();
        $foreign = Order::firstOrFail();
        $this->actingAs($user)->postJson('/service-orders', ['order_id' => $foreign->id, 'tenant_id' => $other->tenant_id])->assertUnprocessable();
        $local = $this->product($user);
        $this->shift($user);
        $this->postJson('/pos', $this->checkout($local))->assertOk();
        $localOrder = Order::where('tenant_id', $user->tenant_id)->firstOrFail();
        $result = $this->postJson('/service-orders', ['order_id' => $localOrder->id, 'tenant_id' => $other->tenant_id])->assertCreated();
        $this->assertSame($user->tenant_id, $result->json('tenant_id'));
        $this->putJson('/service-orders/'.$result->json('id'), ['order_id' => $foreign->id])->assertUnprocessable();
    }

    public function test_service_workflow_is_tenant_scoped_and_does_not_change_payment(): void
    {
        $user = $this->shop();
        $other = $this->shop();
        $service = $this->product($user, ['type' => 'service']);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($service))->assertOk();
        $order = Order::firstOrFail();
        $this->assertSame('queued', $order->service_status);
        $this->get('/services')->assertOk()->assertSee($order->invoice_number);
        $this->patchJson('/services/'.$order->id, ['status' => 'working', 'assigned_user_id' => $other->id])->assertUnprocessable();
        $this->patchJson('/services/'.$order->id, ['status' => 'completed'])->assertStatus(409);
        foreach (['working', 'ready', 'completed'] as $status) {
            $this->patch('/services/'.$order->id, ['status' => $status, 'assigned_user_id' => $user->id])->assertRedirect();
        }
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('completed', $order->fresh()->service_status);
        $this->actingAs($other)->patchJson('/services/'.$order->id, ['status' => 'completed'])->assertNotFound();
    }

    public function test_service_profile_seeds_services_and_import_preserves_item_types(): void
    {
        $user = $this->shop();
        $user->tenant->update(['business_type' => 'service']);
        $this->actingAs($user)->post('/getting-started/sample')->assertRedirect();
        $this->assertSame(4, Product::where('type', 'service')->count());
        $file = UploadedFile::fake()->createWithContent('catalog.csv', "name,category,price,cost,stock,manage_stock,type\nPerbaikan,Jasa,20000,0,99,1,service\nBuku,Barang,8000,5000,10,1,product\n");
        $this->post('/getting-started/import', ['file' => $file])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', ['product_name' => 'Perbaikan', 'type' => 'service', 'stock' => 0, 'manage_stock' => false]);
        $this->assertDatabaseHas('products', ['product_name' => 'Buku', 'type' => 'product', 'stock' => 10]);
    }

    public function test_product_validation_scopes_category_and_forces_non_stock_services(): void
    {
        $user = $this->shop();
        $product = $this->product($user);
        $foreign = $this->product($this->shop());
        $data = ['category_id' => $foreign->category_id, 'sku' => 'SERVICE-TEST', 'product_name' => 'Jasa tes', 'type' => 'service', 'sell_price' => 25000, 'stock' => 100, 'manage_stock' => 1, 'is_active' => 1];
        $this->actingAs($user)->postJson('/products', $data)->assertUnprocessable();
        $data['category_id'] = $product->category_id;
        $this->post('/products', $data)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('products', ['sku' => 'SERVICE-TEST', 'type' => 'service', 'stock' => 0, 'manage_stock' => false]);
        $data['type'] = 'invalid';
        $data['sku'] = $product->sku;
        $this->putJson('/products/'.$product->id, $data)->assertUnprocessable();
    }
}
