<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;

/**
 * @extends Factory<DespesaCaminhao>
 */
class DespesaCaminhaoFactory extends Factory
{
    protected $model = DespesaCaminhao::class;

    public function definition(): array
    {
        return [
            'caminhao_id' => Caminhao::factory(),
            'servico' => fake()->randomElement(['Troca de óleo', 'Troca de pneu', 'Revisão geral', 'Alinhamento e balanceamento', 'Troca de bateria']),
            'valor_pago' => fake()->randomFloat(2, 100, 3000),
            'data' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
