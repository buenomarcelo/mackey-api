<?php

namespace MAC\Models\Motorista\Actions;

use MAC\Models\Motorista\DTO\MotoristaData;
use MAC\Models\Motorista\Motorista;

final class UpdateMotoristaAction
{
    public function handle(Motorista $motorista, MotoristaData $data): Motorista
    {
        $motorista->update($data->toArray());

        return $motorista;
    }
}
