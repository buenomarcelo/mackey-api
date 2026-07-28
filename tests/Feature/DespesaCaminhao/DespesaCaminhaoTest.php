<?php

namespace Tests\Feature\DespesaCaminhao;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\User\User;
use Tests\TestCase;

class DespesaCaminhaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_despesa(): void
    {
        $caminhao = Caminhao::factory()->create();

        $payload = [
            'caminhao_uuid' => $caminhao->uuid,
            'servico' => 'Troca de óleo',
            'valor_pago' => 350.50,
            'data_despesa' => now()->format('Y-m-d'),
        ];

        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/despesas', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.servico', 'Troca de óleo')
            ->assertJsonPath('data.caminhao.uuid', $caminhao->uuid)
            ->assertJsonPath('data.criado_por.uuid', $user->uuid);
        $this->assertArrayNotHasKey('id', $response->json('data'));
    }

    public function test_can_list_despesas_filtered_by_caminhao(): void
    {
        $caminhao = Caminhao::factory()->create();
        DespesaCaminhao::factory()->for($caminhao)->create();
        DespesaCaminhao::factory()->create();

        $response = $this->actingAs(User::factory()->create())->getJson("/api/despesas?caminhao_uuid={$caminhao->uuid}");

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_can_list_despesas_filtered_by_data(): void
    {
        DespesaCaminhao::factory()->create(['data' => '2026-07-10']);
        DespesaCaminhao::factory()->create(['data' => '2026-07-15']);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/despesas?data=2026-07-10');

        $response->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.data_despesa', '2026-07-10');
    }

    public function test_can_delete_despesa(): void
    {
        $despesa = DespesaCaminhao::factory()->create();

        $this->actingAs(User::factory()->create())->deleteJson("/api/despesas/{$despesa->uuid}")->assertNoContent();

        $this->assertDatabaseMissing('despesas_caminhao', ['id' => $despesa->id]);
    }
}
