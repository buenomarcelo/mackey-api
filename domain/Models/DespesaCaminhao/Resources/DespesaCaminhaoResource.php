<?php

namespace MAC\Models\DespesaCaminhao\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MAC\Models\Caminhao\Resources\CaminhaoResource;
use MAC\Models\User\Resources\UserResource;

/** @mixin \MAC\Models\DespesaCaminhao\DespesaCaminhao */
class DespesaCaminhaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'caminhao' => new CaminhaoResource($this->whenLoaded('caminhao')),
            'servico' => $this->servico,
            'valor_pago' => (float) $this->valor_pago,
            'data_despesa' => $this->data?->format('Y-m-d'),
            'criado_por' => new UserResource($this->whenLoaded('criadoPor')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
