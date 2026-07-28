<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Motorista\Motorista;
use MAC\Models\Viagem\Enums\StatusPagamento;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;

/**
 * @extends Factory<Viagem>
 */
class ViagemFactory extends Factory
{
    protected $model = Viagem::class;

    public function definition(): array
    {
        $frete = fake()->randomFloat(2, 1000, 10000);

        return [
            'motorista_id' => Motorista::factory(),
            'caminhao_id' => Caminhao::factory(),
            'data' => fake()->dateTimeBetween('-2 months', 'now'),
            'origem' => fake()->city(),
            'destino' => fake()->city(),
            'contrato' => fake()->numerify('#######'),
            'peso' => fake()->randomFloat(2, 500, 30000),
            'frete' => $frete,
            'entrada' => round($frete * 0.3, 2),
            'valor_2' => 0,
            'valor_3' => 0,
            'pedagio' => fake()->randomFloat(2, 50, 400),
            'motivo' => null,
            'status_viagem' => StatusViagem::AGENDADA,
            'status_pagamento' => StatusPagamento::PENDENTE,
        ];
    }

    public function finalizada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_viagem' => StatusViagem::FINALIZADA,
        ]);
    }
}
