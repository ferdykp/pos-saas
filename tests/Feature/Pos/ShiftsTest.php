<?php

namespace Tests\Feature\Pos;

use App\Models\User;

class ShiftsTest extends PosTestCase
{
    public function test_summary_and_close_only_count_own_shift(): void
    {
        $user = $this->shop();
        $product = $this->product($user);
        $shift = $this->shift($user);
        $other = User::factory()->create(['tenant_id' => $user->tenant_id, 'role' => 'kasir']);
        $otherShift = $this->shift($other);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertOk();
        $this->actingAs($other)->postJson('/pos', $this->checkout($product))->assertOk();
        $this->actingAs($user)->withSession(['active_shift_id' => $otherShift->id])->getJson('/shifts/summary')->assertOk()->assertJsonPath('cash_sales', 10000)->assertJsonPath('cash_expected', 110000);
        $this->postJson('/shifts/close', ['cash_actual' => 110000])->assertOk();
        $this->assertEquals(0, $shift->fresh()->cash_difference);
        $this->assertSame('open', $otherShift->fresh()->status);
        $this->postJson('/pos', $this->checkout($product))->assertUnprocessable();
    }

    public function test_opening_twice_does_not_create_duplicate_shift(): void
    {
        $this->actingAs($this->shop())->postJson('/shifts/open', ['cash_start' => 10000])->assertOk();
        $this->postJson('/shifts/open', ['cash_start' => 10000])->assertUnprocessable();
        $this->assertDatabaseCount('shifts', 1);
    }
}
