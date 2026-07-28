<?php

namespace MAC\Models\Dashboard\Actions;

use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\ContaPagarMotorista\Enums\StatusContaPagar;

final class ComissoesAVencerAction
{
    /**
     * @return array<int, array{conta: ContaPagarMotorista, vencida: bool}>
     */
    public function handle(): array
    {
        return ContaPagarMotorista::query()
            ->with(['motorista', 'viagem'])
            ->where('status', StatusContaPagar::PENDENTE)
            ->get()
            ->map(fn (ContaPagarMotorista $conta) => [
                'conta' => $conta,
                'vencida' => $conta->vencido,
            ])
            ->all();
    }
}
