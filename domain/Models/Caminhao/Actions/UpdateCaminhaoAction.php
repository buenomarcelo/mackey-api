<?php

namespace MAC\Models\Caminhao\Actions;

use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Caminhao\DTO\CaminhaoData;

final class UpdateCaminhaoAction
{
    public function handle(Caminhao $caminhao, CaminhaoData $data): Caminhao
    {
        $caminhao->update($data->toArray());

        return $caminhao;
    }
}
