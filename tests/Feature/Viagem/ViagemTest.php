<?php

namespace Tests\Feature\Viagem;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;
use Tests\TestCase;

class ViagemTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Motorista $motorista, Caminhao $caminhao, array $overrides = []): array
    {
        return array_merge([
            'motorista_uuid' => $motorista->uuid,
            'caminhao_uuid' => $caminhao->uuid,
            'data_viagem' => now()->format('Y-m-d'),
            'origem' => 'Piracicaba',
            'destino' => 'Contagem MG',
            'contrato' => '1554466',
            'peso' => 18000,
            'frete' => 3000,
            'entrada' => 950,
            'valor_2' => 550,
            'valor_3' => 300,
            'pedagio' => 120,
        ], $overrides);
    }

    public function test_authenticated_user_can_create_viagem(): void
    {
        $motorista = Motorista::factory()->create(['percentual_comissao' => 10]);
        $caminhao = Caminhao::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/viagens', $this->payload($motorista, $caminhao));

        $response->assertCreated()
            ->assertJsonPath('data.origem', 'Piracicaba')
            ->assertJsonPath('data.motorista.uuid', $motorista->uuid)
            ->assertJsonPath('data.caminhao.uuid', $caminhao->uuid)
            ->assertJsonPath('data.restante', 1200)
            ->assertJsonPath('data.status_viagem', 'agendada')
            ->assertJsonPath('data.criado_por.uuid', $user->uuid);

        $this->assertArrayNotHasKey('id', $response->json('data'));
    }

    public function test_valor_adicional_is_stored_but_does_not_affect_restante_or_comissao(): void
    {
        $motorista = Motorista::factory()->create(['percentual_comissao' => 10]);
        $caminhao = Caminhao::factory()->create();
        $user = User::factory()->create();

        $payload = $this->payload($motorista, $caminhao, [
            'frete' => 3000,
            'valor_adicional' => 400,
            'status_viagem' => 'finalizada',
        ]);

        $response = $this->actingAs($user)->postJson('/api/viagens', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.valor_adicional', 400)
            ->assertJsonPath('data.restante', 1200);

        $conta = ContaPagarMotorista::first();
        $this->assertEquals(300.0, (float) $conta->valor_comissao);
    }

    public function test_creating_viagem_already_finalizada_generates_comissao_automatically(): void
    {
        $motorista = Motorista::factory()->create(['percentual_comissao' => 10]);
        $caminhao = Caminhao::factory()->create();
        $user = User::factory()->create();

        $payload = $this->payload($motorista, $caminhao, ['frete' => 3000, 'status_viagem' => 'finalizada']);

        $response = $this->actingAs($user)
            ->postJson('/api/viagens', $payload);

        $response->assertCreated()->assertJsonPath('data.status_viagem', 'finalizada');

        $this->assertDatabaseCount('contas_pagar_motorista', 1);
        $conta = ContaPagarMotorista::first();
        $this->assertEquals(300.0, (float) $conta->valor_comissao);
        $this->assertEquals($motorista->id, $conta->motorista_id);
        $this->assertEquals($user->id, $conta->criado_por_id);
    }

    public function test_can_list_viagens_with_filters(): void
    {
        $motorista = Motorista::factory()->create();
        $caminhao = Caminhao::factory()->create();
        Viagem::factory()->for($motorista)->for($caminhao)->create(['status_viagem' => StatusViagem::EM_TRANSITO]);
        Viagem::factory()->create(['status_viagem' => StatusViagem::FINALIZADA]);

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/viagens?status_viagem=em_transito');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_finalizing_viagem_generates_comissao_automatically(): void
    {
        $motorista = Motorista::factory()->create(['percentual_comissao' => 10]);
        $caminhao = Caminhao::factory()->create();
        $viagem = Viagem::factory()->for($motorista)->for($caminhao)->create([
            'frete' => 3000,
            'status_viagem' => StatusViagem::AGENDADA,
        ]);

        $this->assertDatabaseCount('contas_pagar_motorista', 0);

        $payload = $this->payload($motorista, $caminhao, ['frete' => 3000, 'status_viagem' => 'finalizada', 'status_pagamento' => 'pendente']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson("/api/viagens/{$viagem->uuid}", $payload);

        $response->assertOk()->assertJsonPath('data.status_viagem', 'finalizada');

        $this->assertDatabaseCount('contas_pagar_motorista', 1);
        $conta = ContaPagarMotorista::first();
        $this->assertEquals(300.0, (float) $conta->valor_comissao);
        $this->assertEquals($viagem->id, $conta->viagem_id);
        $this->assertEquals($user->id, $conta->criado_por_id);
    }

    public function test_finalizing_twice_does_not_duplicate_comissao(): void
    {
        $motorista = Motorista::factory()->create(['percentual_comissao' => 10]);
        $caminhao = Caminhao::factory()->create();
        $viagem = Viagem::factory()->for($motorista)->for($caminhao)->finalizada()->create(['frete' => 3000]);

        $user = User::factory()->create();
        $payload = $this->payload($motorista, $caminhao, ['frete' => 3000, 'status_viagem' => 'finalizada', 'status_pagamento' => 'pendente']);

        // Simulate that the trip was already finalized once (commission already generated).
        ContaPagarMotorista::create([
            'motorista_id' => $motorista->id,
            'viagem_id' => $viagem->id,
            'valor_comissao' => 300,
        ]);

        $this->actingAs($user)->putJson("/api/viagens/{$viagem->uuid}", $payload)->assertOk();

        $this->assertDatabaseCount('contas_pagar_motorista', 1);
    }
}
