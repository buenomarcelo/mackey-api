<?php

namespace MAC\Models\Viagem\Actions;

use MAC\Models\Caminhao\Caminhao;
use MAC\Models\ContaPagarMotorista\Actions\GerarComissaoAction;
use MAC\Models\Motorista\Motorista;
use MAC\Models\Viagem\DTO\ViagemData;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;

final class UpdateViagemAction
{
    public function __construct(
        private readonly GerarComissaoAction $gerarComissaoAction,
    ) {
    }

    public function handle(Viagem $viagem, ViagemData $data): Viagem
    {
        $motorista = Motorista::query()->where('uuid', $data->motoristaUuid)->firstOrFail();
        $caminhao = Caminhao::query()->where('uuid', $data->caminhaoUuid)->firstOrFail();

        $estavaFinalizada = $viagem->status_viagem === StatusViagem::FINALIZADA;

        $viagem->update([
            ...$data->toArray(),
            'motorista_id' => $motorista->id,
            'caminhao_id' => $caminhao->id,
        ]);

        if (! $estavaFinalizada && $viagem->status_viagem === StatusViagem::FINALIZADA) {
            $this->gerarComissaoAction->handle($viagem);
        }

        return $viagem->load(['motorista', 'caminhao']);
    }
}
