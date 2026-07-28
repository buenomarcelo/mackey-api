<?php

namespace MAC\Models\Abastecimento\Actions;

use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Motorista\Motorista;

final class CalcularSaldoMotoristaAction
{
    public function handle(Motorista $motorista): float
    {
        return round((float) Abastecimento::query()->where('motorista_id', $motorista->id)->sum('valor_sobrando'), 2);
    }
}
