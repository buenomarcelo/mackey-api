<?php

namespace Tests\Feature\Motorista;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use Tests\TestCase;

class MotoristaTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_motoristas(): void
    {
        $this->getJson('/api/motoristas')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_motoristas(): void
    {
        Motorista::factory()->count(3)->create();

        $response = $this->actingAs(User::factory()->create())->getJson('/api/motoristas');

        $response->assertOk()->assertJsonCount(3, 'data');
        $this->assertArrayNotHasKey('id', $response->json('data.0'));
    }

    public function test_can_search_motoristas_by_name(): void
    {
        Motorista::factory()->create(['nome' => 'Willian Silva']);
        Motorista::factory()->create(['nome' => 'Carlos Souza']);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/motoristas?search=Willian');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_list_is_paginated_from_the_backend(): void
    {
        Motorista::factory()->count(5)->create();

        $response = $this->actingAs(User::factory()->create())->getJson('/api/motoristas?per_page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonPath('meta.current_page', 1);

        $page2 = $this->actingAs(User::factory()->create())->getJson('/api/motoristas?per_page=2&page=2');
        $page2->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('meta.current_page', 2);
    }

    public function test_can_sort_by_column_ascending_and_descending(): void
    {
        Motorista::factory()->create(['nome' => 'Bruno']);
        Motorista::factory()->create(['nome' => 'Ana']);
        Motorista::factory()->create(['nome' => 'Carlos']);

        $asc = $this->actingAs(User::factory()->create())->getJson('/api/motoristas?sort_by=nome&descending=0');
        $asc->assertOk();
        $this->assertSame(['Ana', 'Bruno', 'Carlos'], collect($asc->json('data'))->pluck('nome')->all());

        $desc = $this->actingAs(User::factory()->create())->getJson('/api/motoristas?sort_by=nome&descending=1');
        $desc->assertOk();
        $this->assertSame(['Carlos', 'Bruno', 'Ana'], collect($desc->json('data'))->pluck('nome')->all());
    }

    public function test_invalid_sort_column_falls_back_to_default(): void
    {
        Motorista::factory()->create(['nome' => 'Bruno']);
        Motorista::factory()->create(['nome' => 'Ana']);

        $response = $this->actingAs(User::factory()->create())->getJson('/api/motoristas?sort_by=id_da_tabela_secreta');

        $response->assertOk();
        $this->assertSame(['Ana', 'Bruno'], collect($response->json('data'))->pluck('nome')->all());
    }

    public function test_can_create_motorista(): void
    {
        $payload = [
            'nome' => 'João da Silva',
            'cpf' => '12345678901',
            'telefone' => '(11) 91234-5678',
            'cnh_numero' => '00123456789',
            'cnh_categoria' => 'E',
            'cnh_validade' => now()->addYears(2)->format('Y-m-d'),
            'cep' => '01000-000',
            'logradouro' => 'Rua A',
            'numero' => '100',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
            'percentual_comissao' => 10.5,
            'dia_pagamento' => 5,
        ];

        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/motoristas', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.nome', 'João da Silva')
            ->assertJsonPath('data.criado_por.uuid', $user->uuid);
        $this->assertDatabaseHas('motoristas', ['cpf' => '12345678901', 'criado_por_id' => $user->id]);
    }

    public function test_cannot_create_motorista_with_duplicate_cpf(): void
    {
        Motorista::factory()->create(['cpf' => '11122233344']);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/motoristas', [
            'nome' => 'Outro Nome',
            'cpf' => '11122233344',
            'telefone' => '(11) 91234-5678',
            'cnh_numero' => '00123456789',
            'cnh_categoria' => 'E',
            'cnh_validade' => now()->addYears(2)->format('Y-m-d'),
            'percentual_comissao' => 10,
            'dia_pagamento' => 5,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('cpf');
    }

    public function test_can_show_motorista(): void
    {
        $motorista = Motorista::factory()->create();

        $response = $this->actingAs(User::factory()->create())->getJson("/api/motoristas/{$motorista->uuid}");

        $response->assertOk()->assertJsonPath('data.uuid', $motorista->uuid);
    }

    public function test_can_update_motorista(): void
    {
        $motorista = Motorista::factory()->create();

        $response = $this->actingAs(User::factory()->create())->putJson("/api/motoristas/{$motorista->uuid}", [
            'nome' => 'Nome Atualizado',
            'cpf' => $motorista->cpf,
            'telefone' => $motorista->telefone,
            'cnh_numero' => $motorista->cnh_numero,
            'cnh_categoria' => $motorista->cnh_categoria,
            'cnh_validade' => $motorista->cnh_validade->format('Y-m-d'),
            'percentual_comissao' => 12,
            'dia_pagamento' => 10,
        ]);

        $response->assertOk()->assertJsonPath('data.nome', 'Nome Atualizado');
    }

    public function test_deleting_motorista_inactivates_instead_of_removing(): void
    {
        $motorista = Motorista::factory()->create(['ativo' => true]);

        $this->actingAs(User::factory()->create())->deleteJson("/api/motoristas/{$motorista->uuid}")->assertNoContent();

        $this->assertDatabaseHas('motoristas', ['id' => $motorista->id, 'ativo' => false]);
    }
}
