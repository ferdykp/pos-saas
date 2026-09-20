<?php

namespace Tests\Feature\Pos;

use App\Models\Setting;

class ReportAccuracyTest extends PosTestCase
{
    public function test_unknown_cost_is_not_treated_as_free_inventory(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['cost_price' => 0]);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertOk();
        $this->get('/reports')->assertOk()->assertViewHas('missingCosts', 1)
            ->assertViewHas('totalHpp', fn ($value) => $value === null)
            ->assertViewHas('netProfit', fn ($value) => $value === null)->assertSee('Belum tersedia');
    }

    public function test_profit_excludes_tax_and_preserves_cost_at_sale(): void
    {
        $user = $this->shop();
        $product = $this->product($user, ['cost_price' => 4000]);
        $this->shift($user);
        foreach (['tax_active' => '1', 'tax_percentage' => '10'] as $key => $value) {
            Setting::create(['tenant_id' => $user->tenant_id, 'key' => $key, 'value' => $value]);
        }
        $this->actingAs($user)->postJson('/pos', $this->checkout($product, ['grand_total' => 11000, 'paid_amount' => 11000]))->assertOk();
        $product->update(['cost_price' => 9000]);
        $this->get('/reports')->assertOk()->assertViewHas('totalHpp', 4000.0)->assertViewHas('netProfit', 6000.0);
    }
}
