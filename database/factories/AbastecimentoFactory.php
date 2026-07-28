<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Motorista\Motorista;

/**
 * @extends Factory<Abastecimento>
 */
class AbastecimentoFactory extends Factory
{
    protected $model = Abastecimento::class;

    public function definition(): array
    {
        $litragem = fake()->randomFloat(2, 100, 400);
        $valorLitro = fake()->randomFloat(3, 5.5, 6.5);
        $valorEnviado = round($litragem * $valorLitro + fake()->randomFloat(2, -50, 50), 2);

        return [
            'motorista_id' => Motorista::factory(),
            'caminhao_id' => Caminhao::factory(),
            'data' => fake()->dateTimeBetween('-2 months', 'now'),
            'km' => fake()->numberBetween(10000, 300000),
            'litragem' => $litragem,
            'valor_litro' => $valorLitro,
            'valor_enviado' => $valorEnviado,
            'posto' => fake()->company(),
            'valor_sobrando' => round($valorEnviado - ($litragem * $valorLitro), 2),
        ];
    }
}
