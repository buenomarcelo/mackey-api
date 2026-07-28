<?php

namespace MAC\Models\DespesaCaminhao\Actions;

use MAC\Models\Caminhao\Caminhao;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\DespesaCaminhao\DTO\DespesaCaminhaoData;

final class UpdateDespesaCaminhaoAction
{
    public function handle(DespesaCaminhao $despesa, DespesaCaminhaoData $data): DespesaCaminhao
    {
        $caminhao = Caminhao::query()->where('uuid', $data->caminhaoUuid)->firstOrFail();

        $despesa->update([
            ...$data->toArray(),
            'caminhao_id' => $caminhao->id,
        ]);

        return $despesa->load(['caminhao']);
    }
}
