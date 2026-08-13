<?php

namespace Tests\Feature\Balancete;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\Motorista\Motorista;
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
            ->assertJsonPath('data.total_comissoes', 0)
            ->assertJsonPath('data.total_valor_adicional', 0)
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

    public function test_calcula_balancete_inclui_comissao_dos_motoristas(): void
    {
        $caminhao = Caminhao::factory()->create();
        $motorista = Motorista::factory()->create();
        $viagem = Viagem::factory()->for($caminhao)->for($motorista)->create(['frete' => 5000, 'data' => now()]);

        ContaPagarMotorista::factory()->for($motorista)->for($viagem)->create(['valor_comissao' => 500]);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/balancete');

        $response->assertOk()
            ->assertJsonPath('data.total_fretes', 5000)
            ->assertJsonPath('data.total_comissoes', 500)
            ->assertJsonPath('data.resultado_liquido', 4500);
    }

    public function test_calcula_balancete_comissao_filtrada_por_caminhao_e_periodo(): void
    {
        $caminhaoA = Caminhao::factory()->create();
        $caminhaoB = Caminhao::factory()->create();
        $motorista = Motorista::factory()->create();

        $viagemDentro = Viagem::factory()->for($caminhaoA)->for($motorista)->create(['frete' => 3000, 'data' => now()]);
        $viagemForaPeriodo = Viagem::factory()->for($caminhaoA)->for($motorista)->create(['frete' => 2000, 'data' => now()->subMonths(2)]);
        $viagemOutroCaminhao = Viagem::factory()->for($caminhaoB)->for($motorista)->create(['frete' => 4000, 'data' => now()]);

        ContaPagarMotorista::factory()->for($motorista)->for($viagemDentro)->create(['valor_comissao' => 300]);
        ContaPagarMotorista::factory()->for($motorista)->for($viagemForaPeriodo)->create(['valor_comissao' => 200]);
        ContaPagarMotorista::factory()->for($motorista)->for($viagemOutroCaminhao)->create(['valor_comissao' => 400]);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/balancete?' . http_build_query([
            'caminhao_uuid' => $caminhaoA->uuid,
            'data_inicio' => now()->startOfMonth()->format('Y-m-d'),
            'data_fim' => now()->endOfMonth()->format('Y-m-d'),
        ]));

        $response->assertOk()->assertJsonPath('data.total_comissoes', 300);
    }

    public function test_calcula_balancete_inclui_valor_adicional_sem_afetar_comissao(): void
    {
        $caminhao = Caminhao::factory()->create();
        $motorista = Motorista::factory()->create(['percentual_comissao' => 10]);
        $viagem = Viagem::factory()->for($caminhao)->for($motorista)->create([
            'frete' => 5000,
            'valor_adicional' => 700,
            'data' => now(),
        ]);

        ContaPagarMotorista::factory()->for($motorista)->for($viagem)->create(['valor_comissao' => 500]);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/balancete');

        $response->assertOk()
            ->assertJsonPath('data.total_fretes', 5000)
            ->assertJsonPath('data.total_valor_adicional', 700)
            ->assertJsonPath('data.total_comissoes', 500)
            ->assertJsonPath('data.resultado_liquido', 5200);
    }
}
