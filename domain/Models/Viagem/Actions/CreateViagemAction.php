<?php

namespace MAC\Models\Viagem\Actions;

use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Motorista\Motorista;
use MAC\Models\Viagem\DTO\ViagemData;
use MAC\Models\Viagem\Viagem;

final class CreateViagemAction
{
    public function handle(ViagemData $data): Viagem
    {
        $motorista = Motorista::query()->where('uuid', $data->motoristaUuid)->firstOrFail();
        $caminhao = Caminhao::query()->where('uuid', $data->caminhaoUuid)->firstOrFail();

        $viagem = Viagem::create([
            ...$data->toArray(),
            'motorista_id' => $motorista->id,
            'caminhao_id' => $caminhao->id,
            'criado_por_id' => auth()->id(),
        ]);

        return $viagem->load(['motorista', 'caminhao', 'criadoPor']);
    }
}
