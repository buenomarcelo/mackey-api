<?php

namespace MAC\Models\Motorista\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MAC\Models\User\Resources\UserResource;

/** @mixin \MAC\Models\Motorista\Motorista */
class MotoristaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'telefone' => $this->telefone,
            'cnh_numero' => $this->cnh_numero,
            'cnh_categoria' => $this->cnh_categoria,
            'cnh_validade' => $this->cnh_validade?->format('Y-m-d'),
            'cep' => $this->cep,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'percentual_comissao' => (float) $this->percentual_comissao,
            'dia_pagamento' => $this->dia_pagamento,
            'ativo' => $this->ativo,
            'criado_por' => new UserResource($this->whenLoaded('criadoPor')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
