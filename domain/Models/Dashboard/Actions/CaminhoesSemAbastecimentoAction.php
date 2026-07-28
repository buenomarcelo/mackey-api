<?php

namespace MAC\Models\Dashboard\Actions;

use Illuminate\Support\Facades\Date;
use MAC\Models\Caminhao\Caminhao;

final class CaminhoesSemAbastecimentoAction
{
    /**
     * @return array<int, array{caminhao: Caminhao, ultimo_abastecimento: ?string, dias_sem_abastecer: ?int}>
     */
    public function handle(int $dias = 15): array
    {
        $resultado = [];

        foreach (Caminhao::query()->where('ativo', true)->get() as $caminhao) {
            $ultimo = $caminhao->abastecimentos()->orderByDesc('data')->first();

            $diasSemAbastecer = $ultimo ? Date::now()->diffInDays($ultimo->data) : null;

            if (is_null($ultimo) || $diasSemAbastecer >= $dias) {
                $resultado[] = [
                    'caminhao' => $caminhao,
                    'ultimo_abastecimento' => $ultimo?->data?->format('Y-m-d'),
                    'dias_sem_abastecer' => $diasSemAbastecer,
                ];
            }
        }

        return $resultado;
    }
}
