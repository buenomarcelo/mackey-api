<?php

namespace MAC\Models\Motorista\Actions;

use MAC\Models\Motorista\DTO\MotoristaData;
use MAC\Models\Motorista\Motorista;

final class CreateMotoristaAction
{
    public function handle(MotoristaData $data): Motorista
    {
        return Motorista::create([
            ...$data->toArray(),
            'criado_por_id' => auth()->id(),
        ]);
    }
}
