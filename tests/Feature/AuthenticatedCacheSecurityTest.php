<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedCacheSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_invalidates_session_and_rotates_csrf_token(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $oldToken = session()->token();

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertNotSame($oldToken, session()->token());
    }
}
