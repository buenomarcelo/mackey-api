<?php

namespace MAC\Models\Motorista\Actions;

use MAC\Models\Motorista\Motorista;

final class InactivateMotoristaAction
{
    public function handle(Motorista $motorista): void
    {
        $motorista->update(['ativo' => false]);
    }
}
