<?php

namespace MAC\Models\Viagem\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use MAC\Models\Viagem\Enums\StatusPagamento;
use MAC\Models\Viagem\Enums\StatusViagem;

class UpdateViagemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'motorista_uuid' => ['required', 'uuid', 'exists:motoristas,uuid'],
            'caminhao_uuid' => ['required', 'uuid', 'exists:caminhoes,uuid'],
            'data_viagem' => ['required', 'date'],
            'origem' => ['required', 'string', 'max:255'],
            'destino' => ['required', 'string', 'max:255'],
            'contrato' => ['nullable', 'string', 'max:255'],
            'peso' => ['required', 'numeric', 'min:0'],
            'frete' => ['required', 'numeric', 'min:0'],
            'entrada' => ['nullable', 'numeric', 'min:0'],
            'valor_2' => ['nullable', 'numeric', 'min:0'],
            'valor_3' => ['nullable', 'numeric', 'min:0'],
            'pedagio' => ['nullable', 'numeric', 'min:0'],
            'motivo' => ['nullable', 'string'],
            'status_viagem' => ['required', new Enum(StatusViagem::class)],
            'status_pagamento' => ['required', new Enum(StatusPagamento::class)],
        ];
    }
}
