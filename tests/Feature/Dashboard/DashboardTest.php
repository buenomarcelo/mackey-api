<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use MAC\Models\Viagem\Enums\StatusPagamento;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_indicadores_e_alertas(): void
    {
        $motorista = Motorista::factory()->create(['dia_pagamento' => 1]);
        $caminhao = Caminhao::factory()->create();

        Viagem::factory()->for($motorista)->for($caminhao)->create([
            'frete' => 4000,
            'data' => now(),
            'status_viagem' => StatusViagem::FINALIZADA,
            'status_pagamento' => StatusPagamento::PENDENTE,
        ]);

        ContaPagarMotorista::factory()->for($motorista)->create();

        Abastecimento::factory()->for($motorista)->for($caminhao)->create(['valor_sobrando' => -500]);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/dashboard');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'indicadores' => ['total_fretes', 'total_despesas', 'resultado_liquido', 'viagens_em_transito', 'viagens_agendadas'],
                    'alertas' => [
                        'viagens_pagamento_pendente',
                        'comissoes_a_vencer',
                        'motoristas_saldo_negativo',
                        'caminhoes_sem_abastecimento',
                        'caminhoes_consumo_em_queda',
                    ],
                ],
            ]);

        $this->assertGreaterThanOrEqual(1, count($response->json('data.alertas.viagens_pagamento_pendente')));
        $this->assertGreaterThanOrEqual(1, count($response->json('data.alertas.motoristas_saldo_negativo')));
    }
}
