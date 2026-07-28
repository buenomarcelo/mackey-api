<?php

namespace Tests\Feature\Abastecimento;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use Tests\TestCase;

class AbastecimentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_abastecimento_with_valor_sobrando_calculated(): void
    {
        $motorista = Motorista::factory()->create();
        $caminhao = Caminhao::factory()->create();

        $payload = [
            'motorista_uuid' => $motorista->uuid,
            'caminhao_uuid' => $caminhao->uuid,
            'data_abastecimento' => now()->format('Y-m-d'),
            'km' => 100000,
            'litragem' => 200,
            'valor_litro' => 6,
            'valor_enviado' => 1000,
            'posto' => 'Posto Ipiranga',
        ];

        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/abastecimentos', $payload);

        // litragem(200) * valor_litro(6) = 1200; enviado 1000 -> sobrando = 1000 - 1200 = -200
        $response->assertCreated()
            ->assertJsonPath('data.valor_sobrando', -200)
            ->assertJsonPath('data.criado_por.uuid', $user->uuid);
        $this->assertArrayNotHasKey('id', $response->json('data'));
    }

    public function test_consumo_km_l_is_null_for_first_record_and_calculated_from_second(): void
    {
        $caminhao = Caminhao::factory()->create();
        $motorista = Motorista::factory()->create();

        $primeiro = Abastecimento::factory()->for($motorista)->for($caminhao)->create(['km' => 100000, 'litragem' => 100]);
        $segundo = Abastecimento::factory()->for($motorista)->for($caminhao)->create(['km' => 100500, 'litragem' => 100]);

        $response = $this->actingAs(User::factory()->create())->getJson("/api/abastecimentos/{$primeiro->uuid}");
        $response->assertOk()->assertJsonPath('data.consumo_km_l', null);

        $response = $this->actingAs(User::factory()->create())->getJson("/api/abastecimentos/{$segundo->uuid}");
        $response->assertOk()->assertJsonPath('data.consumo_km_l', 5);
    }

    public function test_saldo_do_motorista_soma_valor_sobrando_de_todos_abastecimentos(): void
    {
        $motorista = Motorista::factory()->create();
        Abastecimento::factory()->for($motorista)->create(['valor_sobrando' => 150]);
        Abastecimento::factory()->for($motorista)->create(['valor_sobrando' => -50]);

        $response = $this->actingAs(User::factory()->create())->getJson("/api/motoristas/{$motorista->uuid}/saldo-abastecimento");

        $response->assertOk()->assertJsonPath('data.saldo', 100);
    }

    public function test_can_list_abastecimentos_with_filters(): void
    {
        $motoristaA = Motorista::factory()->create();
        $motoristaB = Motorista::factory()->create();
        $caminhaoA = Caminhao::factory()->create();
        $caminhaoB = Caminhao::factory()->create();

        Abastecimento::factory()->for($motoristaA)->for($caminhaoA)->create(['data' => '2026-07-10']);
        Abastecimento::factory()->for($motoristaB)->for($caminhaoB)->create(['data' => '2026-07-15']);

        $user = User::factory()->create();

        $this->actingAs($user)->getJson("/api/abastecimentos?motorista_uuid={$motoristaA->uuid}")
            ->assertOk()->assertJsonCount(1, 'data');

        $this->actingAs($user)->getJson("/api/abastecimentos?caminhao_uuid={$caminhaoB->uuid}")
            ->assertOk()->assertJsonCount(1, 'data');

        $this->actingAs($user)->getJson('/api/abastecimentos?data=2026-07-10')
            ->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.motorista.uuid', $motoristaA->uuid);
    }

    public function test_saldo_segue_o_motorista_mesmo_trocando_de_caminhao(): void
    {
        $motorista = Motorista::factory()->create();
        $caminhaoA = Caminhao::factory()->create();
        $caminhaoB = Caminhao::factory()->create();

        Abastecimento::factory()->for($motorista)->for($caminhaoA)->create(['valor_sobrando' => 150]);
        Abastecimento::factory()->for($motorista)->for($caminhaoB)->create(['valor_sobrando' => 50]);

        $response = $this->actingAs(User::factory()->create())->getJson("/api/motoristas/{$motorista->uuid}/saldo-abastecimento");

        $response->assertOk()->assertJsonPath('data.saldo', 200);
    }
}
