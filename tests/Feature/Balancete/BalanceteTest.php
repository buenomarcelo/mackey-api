<?php

namespace Tests\Feature\Balancete;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\User\User;
use MAC\Models\Viagem\Viagem;
use Tests\TestCase;

class BalanceteTest extends TestCase
{
    use RefreshDatabase;

    public function test_calcula_balancete_geral(): void
    {
        $caminhao = Caminhao::factory()->create();

        Viagem::factory()->for($caminhao)->create(['frete' => 5000, 'data' => now()]);
        Abastecimento::factory()->for($caminhao)->create(['valor_enviado' => 1000, 'data' => now()]);
        DespesaCaminhao::factory()->for($caminhao)->create(['valor_pago' => 500, 'data' => now()]);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/balancete');

        $response->assertOk()
            ->assertJsonPath('data.total_fretes', 5000)
            ->assertJsonPath('data.total_abastecimento', 1000)
            ->assertJsonPath('data.total_despesas', 500)
            ->assertJsonPath('data.resultado_liquido', 3500);
    }

    public function test_calcula_balancete_filtrado_por_caminhao(): void
    {
        $caminhaoA = Caminhao::factory()->create();
        $caminhaoB = Caminhao::factory()->create();

        Viagem::factory()->for($caminhaoA)->create(['frete' => 3000, 'data' => now()]);
        Viagem::factory()->for($caminhaoB)->create(['frete' => 9000, 'data' => now()]);

        $response = $this->actingAs(User::factory()->create())
            ->getJson("/api/balancete?caminhao_uuid={$caminhaoA->uuid}");

        $response->assertOk()->assertJsonPath('data.total_fretes', 3000);
    }
}
