<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\ContaPagarMotorista\Enums\StatusContaPagar;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use MAC\Models\Viagem\Enums\StatusPagamento;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $motoristas = Motorista::factory(5)->create();
        $caminhoes = Caminhao::factory(5)->create();

        foreach ($caminhoes as $caminhao) {
            $km = 80000;

            for ($i = 0; $i < 4; $i++) {
                $km += rand(400, 900);

                Abastecimento::factory()
                    ->for($motoristas->random())
                    ->for($caminhao)
                    ->create(['km' => $km, 'data' => now()->subDays((4 - $i) * 7)]);
            }

            DespesaCaminhao::factory()->for($caminhao)->count(2)->create();
        }

        foreach ($motoristas as $motorista) {
            foreach ($caminhoes->random(2) as $caminhao) {
                $viagem = Viagem::factory()
                    ->for($motorista)
                    ->for($caminhao)
                    ->create([
                        'status_viagem' => StatusViagem::FINALIZADA,
                        'status_pagamento' => fake()->randomElement([StatusPagamento::PENDENTE, StatusPagamento::PAGO]),
                    ]);

                ContaPagarMotorista::factory()
                    ->for($motorista)
                    ->for($viagem)
                    ->create([
                        'valor_comissao' => round((float) $viagem->frete * ((float) $motorista->percentual_comissao / 100), 2),
                        'status' => $viagem->status_pagamento === StatusPagamento::PAGO ? StatusContaPagar::PAGO : StatusContaPagar::PENDENTE,
                    ]);
            }

            Viagem::factory()->for($motorista)->for($caminhoes->random())->create(['status_viagem' => StatusViagem::EM_TRANSITO]);
            Viagem::factory()->for($motorista)->for($caminhoes->random())->create(['status_viagem' => StatusViagem::AGENDADA]);
        }
    }
}
