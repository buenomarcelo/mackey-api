<?php

namespace MAC\Models\Abastecimento\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MAC\Models\Caminhao\Resources\CaminhaoResource;
use MAC\Models\Motorista\Resources\MotoristaResource;
use MAC\Models\User\Resources\UserResource;

/** @mixin \MAC\Models\Abastecimento\Abastecimento */
class AbastecimentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'motorista' => new MotoristaResource($this->whenLoaded('motorista')),
            'caminhao' => new CaminhaoResource($this->whenLoaded('caminhao')),
            'data_abastecimento' => $this->data?->format('Y-m-d'),
            'km' => $this->km,
            'litragem' => (float) $this->litragem,
            'valor_litro' => (float) $this->valor_litro,
            'valor_enviado' => (float) $this->valor_enviado,
            'posto' => $this->posto,
            'valor_sobrando' => (float) $this->valor_sobrando,
            'consumo_km_l' => $this->resource->consumoKmL(),
            'criado_por' => new UserResource($this->whenLoaded('criadoPor')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
