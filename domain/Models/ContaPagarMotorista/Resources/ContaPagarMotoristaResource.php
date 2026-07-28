<?php

namespace MAC\Models\ContaPagarMotorista\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MAC\Models\Motorista\Resources\MotoristaResource;
use MAC\Models\User\Resources\UserResource;
use MAC\Models\Viagem\Resources\ViagemResource;

/** @mixin \MAC\Models\ContaPagarMotorista\ContaPagarMotorista */
class ContaPagarMotoristaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'motorista' => new MotoristaResource($this->whenLoaded('motorista')),
            'viagem' => new ViagemResource($this->whenLoaded('viagem')),
            'valor_comissao' => (float) $this->valor_comissao,
            'status' => $this->status->value,
            'data_pagamento' => $this->data_pagamento?->format('Y-m-d'),
            'data_vencimento' => $this->data_vencimento->format('Y-m-d'),
            'vencido' => $this->vencido,
            'criado_por' => new UserResource($this->whenLoaded('criadoPor')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
