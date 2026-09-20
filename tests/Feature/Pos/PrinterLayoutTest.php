<?php

namespace Tests\Feature\Pos;

use App\Models\Order;

class PrinterLayoutTest extends PosTestCase
{
    public function test_receipts_support_two_paper_sizes_and_reject_invalid_css_input(): void
    {
        $user = $this->shop();
        $product = $this->product($user);
        $this->shift($user);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertOk();
        $order = Order::firstOrFail();
        foreach ([58, 80] as $width) {
            $this->get('/orders/'.$order->id.'/print?paper='.$width)->assertOk()->assertSee('class="paper-'.$width.'"', false)
                ->assertSee('width: '.$width.'mm;', false)
                ->assertSee('box-sizing: border-box;', false)
                ->assertSee('.print-actions { display: none !important; }', false)
                ->assertDontSee('/build/', false)
                ->assertDontSee('<link', false);
        }
        $this->getJson('/orders/'.$order->id.'/print?paper=999')->assertUnprocessable();
        $this->actingAs($this->shop())->get('/orders/'.$order->id.'/print?paper=80')->assertNotFound();
    }
}
