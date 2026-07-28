<?php

namespace MAC\Models\DespesaCaminhao\Actions;

use MAC\Models\Caminhao\Caminhao;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\DespesaCaminhao\DTO\DespesaCaminhaoData;

final class CreateDespesaCaminhaoAction
{
    public function handle(DespesaCaminhaoData $data): DespesaCaminhao
    {
        $caminhao = Caminhao::query()->where('uuid', $data->caminhaoUuid)->firstOrFail();

        $despesa = DespesaCaminhao::create([
            ...$data->toArray(),
            'caminhao_id' => $caminhao->id,
            'criado_por_id' => auth()->id(),
        ]);

        return $despesa->load(['caminhao', 'criadoPor']);
    }
}
