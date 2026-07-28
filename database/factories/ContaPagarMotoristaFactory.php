<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\Motorista\Motorista;
use MAC\Models\Viagem\Viagem;

/**
 * @extends Factory<ContaPagarMotorista>
 */
class ContaPagarMotoristaFactory extends Factory
{
    protected $model = ContaPagarMotorista::class;

    public function definition(): array
    {
        return [
            'motorista_id' => Motorista::factory(),
            'viagem_id' => Viagem::factory(),
            'valor_comissao' => fake()->randomFloat(2, 100, 1000),
        ];
    }
}
