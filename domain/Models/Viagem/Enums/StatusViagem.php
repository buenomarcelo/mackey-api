<?php

namespace MAC\Models\Viagem\Enums;

enum StatusViagem: string
{
    case AGENDADA = 'agendada';
    case EM_TRANSITO = 'em_transito';
    case FINALIZADA = 'finalizada';
    case CANCELADA = 'cancelada';
}
