<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CafePreviewSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || config('database.default') !== 'sqlite' || ! str_contains(config('database.connections.sqlite.database'), 'growpos-preview')) {
            throw new \RuntimeException('Demo hanya diizinkan pada SQLite growpos-preview lokal.');
        }
        $password = getenv('GROWPOS_DEMO_PASSWORD');
        if (! $password) {
            throw new \RuntimeException('Set GROWPOS_DEMO_PASSWORD untuk akun demo lokal.');
        }
        $user = User::firstOrCreate(['email' => 'preview@growpos.test'], ['name' => 'Nadia', 'password' => Hash::make($password), 'email_verified_at' => now(), 'role' => 'admin']);
        $tenant = Tenant::firstOrCreate(['user_id' => $user->id], ['name' => 'Kedai Sore', 'slug' => 'kedai-sore', 'business_type' => 'Kedai kopi', 'email' => $user->email, 'phone' => '0800000000', 'address' => 'Bandung']);
        $user->update(['tenant_id' => $tenant->id]);
        $plan = Plan::firstOrCreate(['slug' => 'growth'], ['name' => 'Growth', 'price' => 99000, 'max_products' => 100, 'max_users' => 5, 'max_outlets' => 3]);
        Subscription::firstOrCreate(['tenant_id' => $tenant->id], ['plan_id' => $plan->id, 'start_date' => now(), 'end_date' => now()->addMonth(), 'status' => 'active']);
        $categories = [];
        foreach (['Kopi', 'Non-kopi', 'Makanan'] as $name) {
            $categories[$name] = Category::firstOrCreate(['name' => $name, 'tenant_id' => $tenant->id], ['slug' => Str::slug($name)]);
        }
        foreach ([['Kopi Susu Gula Aren', 'Kopi', 22000, 30], ['Americano', 'Kopi', 18000, 20], ['Cappuccino', 'Kopi', 24000, 15], ['Cafe Latte', 'Kopi', 24000, 25], ['Matcha Latte', 'Non-kopi', 26000, 18], ['Cokelat Hangat', 'Non-kopi', 23000, 20], ['Teh Lemon', 'Non-kopi', 16000, 3], ['Es Teh Leci', 'Non-kopi', 18000, 22], ['Croissant Butter', 'Makanan', 20000, 10], ['Roti Bakar Cokelat', 'Makanan', 18000, 20], ['French Fries', 'Makanan', 22000, 20], ['Pisang Goreng', 'Makanan', 16000, 15]] as [$name,$category,$price,$stock]) {
            Product::firstOrCreate(['tenant_id' => $tenant->id, 'product_name' => $name], ['category_id' => $categories[$category]->id, 'sku' => Str::slug($name), 'sell_price' => $price, 'cost_price' => $price * .35, 'stock' => $stock, 'min_stock' => 5, 'manage_stock' => true, 'is_active' => true]);
        }
        $product = Product::first();
        $product->variants()->firstOrCreate(['name' => 'Large'], ['sku' => 'kopi-large', 'price' => 27000, 'stock' => 15]);
        $product->addons()->firstOrCreate(['name' => 'Extra shot'], ['price' => 5000, 'cost' => 1500]);
        $product->addons()->firstOrCreate(['name' => 'Oat milk'], ['price' => 6000, 'cost' => 3000]);
        Customer::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Rani'], ['is_member' => true]);
        $shift = Shift::firstOrCreate(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'status' => 'open'], ['start_time' => now()->subHours(4), 'cash_start' => 200000, 'cash_expected' => 200000]);
        if (! Order::exists()) {
            foreach (range(1, 12) as $i) {
                $p = Product::find(($i % 12) + 1);
                $o = Order::create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'shift_id' => $shift->id, 'invoice_number' => 'DEMO-'.$i, 'subtotal' => $p->sell_price * 2, 'grand_total' => $p->sell_price * 2, 'paid_amount' => $p->sell_price * 2, 'payment_status' => 'paid', 'payment_method' => 'cash', 'order_status' => 'completed', 'order_type' => 'dine_in', 'table_number' => (string) ($i % 5 + 1), 'kitchen_status' => $i > 9 ? 'queued' : 'served', 'sold_at' => now()->subMinutes($i * 8)]);
                $o->items()->create(['product_id' => $p->id, 'product_name' => $p->product_name, 'quantity' => 2, 'price' => $p->sell_price, 'subtotal' => $p->sell_price * 2, 'unit_cost' => $p->cost_price]);
            }
        }
    }
}
