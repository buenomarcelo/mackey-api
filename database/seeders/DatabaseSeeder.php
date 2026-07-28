<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;

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
        }
    }
}
