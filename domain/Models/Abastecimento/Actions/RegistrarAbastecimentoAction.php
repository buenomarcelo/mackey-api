<?php

namespace MAC\Models\Abastecimento\Actions;

use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Abastecimento\DTO\AbastecimentoData;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Motorista\Motorista;

final class RegistrarAbastecimentoAction
{
    public function handle(AbastecimentoData $data): Abastecimento
    {
        $motorista = Motorista::query()->where('uuid', $data->motoristaUuid)->firstOrFail();
        $caminhao = Caminhao::query()->where('uuid', $data->caminhaoUuid)->firstOrFail();

        $valorSobrando = $data->valorEnviado - ($data->litragem * $data->valorLitro);

        $abastecimento = Abastecimento::create([
            ...$data->toArray(),
            'motorista_id' => $motorista->id,
            'caminhao_id' => $caminhao->id,
            'valor_sobrando' => round($valorSobrando, 2),
            'criado_por_id' => auth()->id(),
        ]);

        return $abastecimento->load(['motorista', 'caminhao', 'criadoPor']);
    }
}
