<?php

namespace MAC\Models\Viagem\Actions;

use MAC\Models\Caminhao\Caminhao;
use MAC\Models\ContaPagarMotorista\Actions\GerarComissaoAction;
use MAC\Models\Motorista\Motorista;
use MAC\Models\Viagem\DTO\ViagemData;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;

final class CreateViagemAction
{
    public function __construct(
        private readonly GerarComissaoAction $gerarComissaoAction,
    ) {
    }

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

        if ($viagem->status_viagem === StatusViagem::FINALIZADA) {
            $this->gerarComissaoAction->handle($viagem);
        }

        return $viagem->load(['motorista', 'caminhao', 'criadoPor']);
    }
}
