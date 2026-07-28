<?php

namespace MAC\Models\Caminhao\Actions;

use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Caminhao\DTO\CaminhaoData;

final class CreateCaminhaoAction
{
    public function handle(CaminhaoData $data): Caminhao
    {
        return Caminhao::create([
            ...$data->toArray(),
            'criado_por_id' => auth()->id(),
        ]);
    }
}
