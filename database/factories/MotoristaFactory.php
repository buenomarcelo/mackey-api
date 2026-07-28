<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MAC\Models\Motorista\Motorista;

/**
 * @extends Factory<Motorista>
 */
class MotoristaFactory extends Factory
{
    protected $model = Motorista::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###########'),
            'telefone' => fake()->numerify('(##) #####-####'),
            'cnh_numero' => fake()->numerify('###########'),
            'cnh_categoria' => fake()->randomElement(['A', 'B', 'AB', 'C', 'D', 'E']),
            'cnh_validade' => fake()->dateTimeBetween('+6 months', '+5 years'),
            'cep' => fake()->numerify('#####-###'),
            'logradouro' => fake()->streetName(),
            'numero' => fake()->buildingNumber(),
            'cidade' => fake()->city(),
            'uf' => fake()->stateAbbr(),
            'percentual_comissao' => fake()->randomFloat(2, 5, 15),
            'dia_pagamento' => fake()->numberBetween(1, 28),
            'ativo' => true,
        ];
    }
}
