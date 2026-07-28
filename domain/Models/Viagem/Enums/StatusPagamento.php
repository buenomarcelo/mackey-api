<?php

namespace MAC\Models\Viagem\Enums;

enum StatusPagamento: string
{
    case PENDENTE = 'pendente';
    case PAGO_PARCIAL = 'pago_parcial';
    case PAGO = 'pago';
}
