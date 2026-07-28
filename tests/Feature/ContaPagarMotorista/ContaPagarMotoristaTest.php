<?php

namespace Tests\Feature\ContaPagarMotorista;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\ContaPagarMotorista\Enums\StatusContaPagar;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;
use Tests\TestCase;

class ContaPagarMotoristaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_contas_pagar_filtering_by_motorista(): void
    {
        $motorista = Motorista::factory()->create();
        $viagem = Viagem::factory()->for($motorista)->finalizada()->create();
        ContaPagarMotorista::factory()->for($motorista)->for($viagem)->create();
        ContaPagarMotorista::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->getJson("/api/contas-pagar?motorista_uuid={$motorista->uuid}");

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_can_list_contas_pagar_filtering_by_data_vencimento(): void
    {
        $motorista = Motorista::factory()->create(['dia_pagamento' => 15]);

        $this->travelTo('2026-07-01');
        $contaJulho = ContaPagarMotorista::factory()->for($motorista)->create();

        $this->travelTo('2026-08-01');
        ContaPagarMotorista::factory()->for($motorista)->create();

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/contas-pagar?data_vencimento=2026-07-15');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.uuid', $contaJulho->uuid);
    }

    public function test_resumo_agrupa_total_pendente_por_motorista_no_mes_de_vencimento(): void
    {
        $motoristaA = Motorista::factory()->create(['dia_pagamento' => 15]);
        $motoristaB = Motorista::factory()->create(['dia_pagamento' => 20]);

        $this->travelTo('2026-07-01');
        ContaPagarMotorista::factory()->for($motoristaA)->create(['valor_comissao' => 100]);
        ContaPagarMotorista::factory()->for($motoristaA)->create(['valor_comissao' => 200]);
        ContaPagarMotorista::factory()->for($motoristaB)->create(['valor_comissao' => 500]);

        // paga: não deve entrar no total pendente
        ContaPagarMotorista::factory()->for($motoristaA)->create(['valor_comissao' => 999, 'status' => StatusContaPagar::PAGO]);

        // vencimento em agosto: não deve entrar no resumo de julho
        $this->travelTo('2026-08-01');
        ContaPagarMotorista::factory()->for($motoristaA)->create(['valor_comissao' => 700]);

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/contas-pagar/resumo?ano=2026&mes=7');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.motorista_uuid', $motoristaB->uuid)
            ->assertJsonPath('data.0.total_pendente', 500)
            ->assertJsonPath('data.0.quantidade', 1)
            ->assertJsonPath('data.1.motorista_uuid', $motoristaA->uuid)
            ->assertJsonPath('data.1.total_pendente', 300)
            ->assertJsonPath('data.1.quantidade', 2);
    }

    public function test_can_mark_conta_as_paid(): void
    {
        $conta = ContaPagarMotorista::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->patchJson("/api/contas-pagar/{$conta->uuid}/pagar");

        $response->assertOk()
            ->assertJsonPath('data.status', 'pago')
            ->assertJsonPath('data.data_pagamento', now()->format('Y-m-d'));
    }

    public function test_can_download_recibo_pdf(): void
    {
        $conta = ContaPagarMotorista::factory()->create();

        $response = $this->actingAs(User::factory()->create())
            ->get("/api/contas-pagar/{$conta->uuid}/recibo");

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_conta_pendente_recente_nao_esta_vencida(): void
    {
        $motorista = Motorista::factory()->create(['dia_pagamento' => 28]);
        $this->travelTo(now()->startOfMonth()->addDays(4));

        $conta = ContaPagarMotorista::factory()->for($motorista)->create();

        $response = $this->actingAs(User::factory()->create())
            ->getJson("/api/contas-pagar/{$conta->uuid}");

        $response->assertOk()->assertJsonPath('data.vencido', false);
    }

    public function test_conta_pendente_apos_data_vencimento_esta_vencida(): void
    {
        $motorista = Motorista::factory()->create(['dia_pagamento' => 5]);
        $this->travelTo(now()->startOfMonth()->addDays(9));

        $conta = ContaPagarMotorista::factory()->for($motorista)->create();

        $this->travelTo(now()->addMonthNoOverflow());

        $response = $this->actingAs(User::factory()->create())
            ->getJson("/api/contas-pagar/{$conta->uuid}");

        $response->assertOk()->assertJsonPath('data.vencido', true);
    }

    public function test_conta_paga_nunca_esta_vencida(): void
    {
        $motorista = Motorista::factory()->create(['dia_pagamento' => 1]);
        $conta = ContaPagarMotorista::factory()->for($motorista)->create();

        $this->travelTo(now()->addMonths(2));
        $this->actingAs(User::factory()->create())->patchJson("/api/contas-pagar/{$conta->uuid}/pagar");

        $response = $this->actingAs(User::factory()->create())
            ->getJson("/api/contas-pagar/{$conta->uuid}");

        $response->assertOk()->assertJsonPath('data.vencido', false);
    }
}
