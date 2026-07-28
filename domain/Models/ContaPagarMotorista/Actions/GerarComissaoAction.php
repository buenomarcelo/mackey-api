<?php

namespace MAC\Models\ContaPagarMotorista\Actions;

use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\Viagem\Viagem;

final class GerarComissaoAction
{
    public function handle(Viagem $viagem): ContaPagarMotorista
    {
        $existente = ContaPagarMotorista::query()->where('viagem_id', $viagem->id)->first();

        if ($existente) {
            return $existente;
        }

        $valorComissao = (float) $viagem->frete * ((float) $viagem->motorista->percentual_comissao / 100);

        return ContaPagarMotorista::create([
            'motorista_id' => $viagem->motorista_id,
            'viagem_id' => $viagem->id,
            'valor_comissao' => round($valorComissao, 2),
            'criado_por_id' => auth()->id(),
        ]);
    }
}
