<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use MAC\Models\User\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertJsonStructure(['data' => ['uuid', 'name', 'email']]);
        $this->assertArrayNotHasKey('id', $response->json('data'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
    }

    public function test_authenticated_user_can_fetch_own_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertOk()->assertJsonPath('data.uuid', $user->uuid);
    }

    public function test_guest_cannot_fetch_profile(): void
    {
        $this->getJson('/api/auth/me')->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/auth/logout')->assertNoContent();

        // Sanctum's RequestGuard caches the resolved user for the lifetime of the
        // container, so a *new* simulated request here would still read as
        // authenticated — that caching doesn't survive across real requests
        // (each is a fresh PHP-FPM process), only across chained test calls.
        // Checking the 'web' guard directly is what actually flips on logout.
        $this->assertFalse(Auth::guard('web')->check());
    }
}
