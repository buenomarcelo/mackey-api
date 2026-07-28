<?php

namespace MAC\Models\Abastecimento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbastecimentoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'motorista_uuid' => ['required', 'uuid', 'exists:motoristas,uuid'],
            'caminhao_uuid' => ['required', 'uuid', 'exists:caminhoes,uuid'],
            'data_abastecimento' => ['required', 'date'],
            'km' => ['nullable', 'integer', 'min:0'],
            'litragem' => ['required', 'numeric', 'min:0.01'],
            'valor_litro' => ['required', 'numeric', 'min:0'],
            'valor_enviado' => ['required', 'numeric', 'min:0'],
            'posto' => ['required', 'string', 'max:255'],
        ];
    }
}
