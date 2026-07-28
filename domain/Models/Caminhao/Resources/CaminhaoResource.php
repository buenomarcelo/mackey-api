<?php

namespace MAC\Models\Caminhao\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MAC\Models\User\Resources\UserResource;

/** @mixin \MAC\Models\Caminhao\Caminhao */
class CaminhaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'placa' => $this->placa,
            'modelo' => $this->modelo,
            'marca' => $this->marca,
            'ano' => $this->ano,
            'capacidade_carga' => (float) $this->capacidade_carga,
            'renavam' => $this->renavam,
            'cor' => $this->cor,
            'ativo' => $this->ativo,
            'criado_por' => new UserResource($this->whenLoaded('criadoPor')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
