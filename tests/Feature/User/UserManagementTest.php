<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use MAC\Models\User\User;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_users(): void
    {
        $this->getJson('/api/usuarios')->assertStatus(401);
    }

    public function test_non_admin_cannot_list_users(): void
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/api/usuarios');

        $response->assertStatus(403);
    }

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->admin()->create())->getJson('/api/usuarios');

        $response->assertOk();
        $this->assertArrayNotHasKey('id', $response->json('data.0'));
    }

    public function test_admin_can_create_user(): void
    {
        $payload = [
            'name' => 'Novo Usuário',
            'email' => 'novo@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
            'is_admin' => false,
        ];

        $response = $this->actingAs(User::factory()->admin()->create())->postJson('/api/usuarios', $payload);

        $response->assertCreated()->assertJsonPath('data.email', 'novo@example.com');
        $this->assertDatabaseHas('users', ['email' => 'novo@example.com', 'is_admin' => false]);
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $response = $this->actingAs(User::factory()->create())->postJson('/api/usuarios', [
            'name' => 'Novo Usuário',
            'email' => 'novo@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password-antiga')]);
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->putJson("/api/usuarios/{$user->uuid}", [
            'name' => 'Nome Atualizado',
            'email' => $user->email,
            'is_admin' => false,
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'Nome Atualizado');
        $this->assertTrue(Hash::check('password-antiga', $user->fresh()->password));
    }

    public function test_admin_cannot_remove_their_own_admin_flag(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->putJson("/api/usuarios/{$admin->uuid}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'is_admin' => false,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('is_admin');
        $this->assertTrue($admin->fresh()->is_admin);
    }

    public function test_admin_cannot_inactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/usuarios/{$admin->uuid}");

        $response->assertStatus(422)->assertJsonValidationErrors('ativo');
        $this->assertTrue($admin->fresh()->ativo);
    }

    public function test_deleting_user_inactivates_instead_of_removing(): void
    {
        $user = User::factory()->create(['ativo' => true]);

        $this->actingAs(User::factory()->admin()->create())
            ->deleteJson("/api/usuarios/{$user->uuid}")
            ->assertNoContent();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'ativo' => false]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password'), 'ativo' => false]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $this->assertGuest();
    }

    /**
     * Regression test for a bug where an admin changing their own password via
     * /api/usuarios got force-logged-out shortly after. Root cause: the
     * auth:sanctum middleware switches the default guard via Auth::shouldUse('sanctum'),
     * so a bare auth()->setUser() after saving the new password left the 'web'
     * guard (which Sanctum's AuthenticateSession middleware checks) holding a
     * stale, pre-update password hash — causing the very next request to be
     * rejected as a session/password mismatch. UpdateUserAction must refresh
     * auth('web') explicitly for this invariant to hold.
     */
    public function test_admin_changing_own_password_refreshes_the_web_guard(): void
    {
        $admin = User::factory()->admin()->create(['password' => bcrypt('senha-antiga')]);

        $this->actingAs($admin, 'web');

        $this->putJson("/api/usuarios/{$admin->uuid}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => 'senha-nova-1234',
            'password_confirmation' => 'senha-nova-1234',
            'is_admin' => true,
        ])->assertOk();

        $this->assertTrue(
            Hash::check('senha-nova-1234', Auth::guard('web')->user()->password),
            "The 'web' guard still holds the pre-update password hash after the request finished; ".
            'the next real request would be force-logged-out by AuthenticateSession.'
        );
    }
}
