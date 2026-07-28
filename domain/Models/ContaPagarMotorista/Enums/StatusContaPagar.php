<?php

namespace MAC\Models\ContaPagarMotorista\Enums;

enum StatusContaPagar: string
{
    case PENDENTE = 'pendente';
    case PAGO = 'pago';
}
