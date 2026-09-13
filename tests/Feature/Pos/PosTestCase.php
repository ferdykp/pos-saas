<?php

namespace Tests\Feature\Pos;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class PosTestCase extends TestCase
{
    use RefreshDatabase;

    protected function shop(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $tenant = Tenant::create(['user_id' => $user->id, 'name' => 'Toko', 'business_type' => 'retail', 'slug' => 'toko-'.$user->id, 'email' => $user->email, 'phone' => '0800', 'address' => 'Jakarta']);
        $user->update(['tenant_id' => $tenant->id]);
        $plan = Plan::firstOrCreate(['slug' => 'growth'], ['name' => 'Growth', 'price' => 100000]);
        Subscription::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'plan_id' => $plan->id, 'start_date' => now(), 'end_date' => now()->addMonth(), 'status' => 'active']);

        return $user->fresh();
    }

    protected function product(User $user, array $attributes = []): Product
    {
        $category = Category::withoutGlobalScopes()->create(['tenant_id' => $user->tenant_id, 'name' => 'Minuman', 'slug' => uniqid('minuman-')]);

        return Product::withoutGlobalScopes()->create(array_merge(['tenant_id' => $user->tenant_id, 'category_id' => $category->id, 'sku' => uniqid('SKU-'), 'product_name' => 'Kopi', 'sell_price' => 10000, 'stock' => 10, 'type' => 'product', 'is_active' => true], $attributes));
    }

    protected function shift(User $user): Shift
    {
        return Shift::withoutGlobalScopes()->create(['tenant_id' => $user->tenant_id, 'user_id' => $user->id, 'start_time' => now()->subMinute(), 'cash_start' => 100000, 'cash_expected' => 100000, 'status' => 'open']);
    }

    protected function checkout(Product $product, array $overrides = []): array
    {
        return array_replace(['payment_method' => 'cash', 'payment_status' => 'paid', 'paid_amount' => 10000, 'grand_total' => 10000, 'items' => [['id' => $product->id, 'quantity' => 1]], 'order_type' => 'takeaway'], $overrides);
    }
}
