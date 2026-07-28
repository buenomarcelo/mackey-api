<?php

namespace Tests\Feature\Caminhao;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\User\User;
use Tests\TestCase;

class CaminhaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_caminhoes(): void
    {
        $this->getJson('/api/caminhoes')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_caminhoes(): void
    {
        Caminhao::factory()->count(2)->create();

        $response = $this->actingAs(User::factory()->create())->getJson('/api/caminhoes');

        $response->assertOk()->assertJsonCount(2, 'data');
        $this->assertArrayNotHasKey('id', $response->json('data.0'));
    }

    public function test_can_create_caminhao(): void
    {
        $payload = [
            'placa' => 'abc1d23',
            'modelo' => 'Actros',
            'marca' => 'Mercedes-Benz',
            'ano' => 2022,
            'capacidade_carga' => 25000,
            'renavam' => '12345678901',
            'cor' => 'Branco',
        ];

        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/caminhoes', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.placa', 'ABC1D23')
            ->assertJsonPath('data.criado_por.uuid', $user->uuid);
        $this->assertDatabaseHas('caminhoes', ['placa' => 'ABC1D23', 'criado_por_id' => $user->id]);
    }

    public function test_cannot_create_caminhao_with_duplicate_placa(): void
    {
        Caminhao::factory()->create(['placa' => 'XYZ9Z99']);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/caminhoes', [
            'placa' => 'XYZ9Z99',
            'modelo' => 'Actros',
            'marca' => 'Mercedes-Benz',
            'ano' => 2022,
            'capacidade_carga' => 25000,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('placa');
    }

    public function test_can_update_caminhao(): void
    {
        $caminhao = Caminhao::factory()->create();

        $response = $this->actingAs(User::factory()->create())->putJson("/api/caminhoes/{$caminhao->uuid}", [
            'placa' => $caminhao->placa,
            'modelo' => 'Modelo Atualizado',
            'marca' => $caminhao->marca,
            'ano' => $caminhao->ano,
            'capacidade_carga' => $caminhao->capacidade_carga,
        ]);

        $response->assertOk()->assertJsonPath('data.modelo', 'Modelo Atualizado');
    }

    public function test_deleting_caminhao_inactivates_instead_of_removing(): void
    {
        $caminhao = Caminhao::factory()->create(['ativo' => true]);

        $this->actingAs(User::factory()->create())->deleteJson("/api/caminhoes/{$caminhao->uuid}")->assertNoContent();

        $this->assertDatabaseHas('caminhoes', ['id' => $caminhao->id, 'ativo' => false]);
    }
}
