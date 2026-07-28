<?php

namespace MAC\Models\Dashboard\Actions;

use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Motorista\Motorista;

final class MotoristasSaldoNegativoAction
{
    /**
     * @return array<int, array{motorista: Motorista, saldo: float}>
     */
    public function handle(float $limite = -200): array
    {
        return Abastecimento::query()
            ->selectRaw('motorista_id, SUM(valor_sobrando) as saldo')
            ->groupBy('motorista_id')
            ->havingRaw('SUM(valor_sobrando) < ?', [$limite])
            ->get()
            ->map(fn ($row) => [
                'motorista' => Motorista::query()->find($row->motorista_id),
                'saldo' => round((float) $row->saldo, 2),
            ])
            ->all();
    }
}
