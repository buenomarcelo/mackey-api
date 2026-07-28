<?php

namespace MAC\Models\Viagem\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MAC\Models\Caminhao\Resources\CaminhaoResource;
use MAC\Models\Motorista\Resources\MotoristaResource;
use MAC\Models\User\Resources\UserResource;

/** @mixin \MAC\Models\Viagem\Viagem */
class ViagemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'motorista' => new MotoristaResource($this->whenLoaded('motorista')),
            'caminhao' => new CaminhaoResource($this->whenLoaded('caminhao')),
            'data_viagem' => $this->data?->format('Y-m-d'),
            'origem' => $this->origem,
            'destino' => $this->destino,
            'contrato' => $this->contrato,
            'peso' => (float) $this->peso,
            'frete' => (float) $this->frete,
            'entrada' => (float) $this->entrada,
            'valor_2' => (float) $this->valor_2,
            'valor_3' => (float) $this->valor_3,
            'pedagio' => (float) $this->pedagio,
            'restante' => (float) $this->restante,
            'motivo' => $this->motivo,
            'status_viagem' => $this->status_viagem->value,
            'status_pagamento' => $this->status_pagamento->value,
            'criado_por' => new UserResource($this->whenLoaded('criadoPor')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
