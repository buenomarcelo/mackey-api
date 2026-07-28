<?php

namespace MAC\Models\ContaPagarMotorista\Actions;

use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\ContaPagarMotorista\Enums\StatusContaPagar;

final class MarcarComoPagoAction
{
    public function handle(ContaPagarMotorista $conta): ContaPagarMotorista
    {
        $conta->update([
            'status' => StatusContaPagar::PAGO,
            'data_pagamento' => now()->format('Y-m-d'),
        ]);

        return $conta;
    }
}
