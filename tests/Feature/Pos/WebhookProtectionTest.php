<?php

namespace Tests\Feature\Pos;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class WebhookProtectionTest extends PosTestCase
{
    public function test_only_webhook_is_exempt_from_csrf_in_real_middleware(): void
    {
        $this->app->bind(PreventRequestForgery::class, fn ($app) => new class($app, $app['encrypter']) extends PreventRequestForgery
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });
        // Reaching signature validation proves CSRF did not reject the webhook.
        config(['services.midtrans.server_key' => 'secret']);
        $this->postJson('/midtrans/callback', ['order_id' => 'INV-test', 'transaction_status' => 'settlement', 'status_code' => '200', 'gross_amount' => '10000', 'signature_key' => 'invalid'])->assertForbidden();
        $this->postJson('/login', ['email' => 'test@example.test', 'password' => 'password'])->assertStatus(419);
    }
}
