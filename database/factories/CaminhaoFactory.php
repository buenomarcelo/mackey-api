<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MAC\Models\Caminhao\Caminhao;

/**
 * @extends Factory<Caminhao>
 */
class CaminhaoFactory extends Factory
{
    protected $model = Caminhao::class;

    public function definition(): array
    {
        return [
            'placa' => strtoupper(fake()->unique()->bothify('???#?##')),
            'modelo' => fake()->randomElement(['Actros', 'FH 540', 'Constellation', 'Delivery', 'Axor']),
            'marca' => fake()->randomElement(['Mercedes-Benz', 'Volvo', 'Volkswagen', 'Scania', 'Iveco']),
            'ano' => fake()->numberBetween(2010, 2026),
            'capacidade_carga' => fake()->randomFloat(2, 5000, 40000),
            'renavam' => fake()->numerify('###########'),
            'cor' => fake()->randomElement(['Branco', 'Prata', 'Azul', 'Vermelho']),
            'ativo' => true,
        ];
    }
}
