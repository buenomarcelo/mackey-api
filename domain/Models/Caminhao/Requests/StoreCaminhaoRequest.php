<?php

namespace MAC\Models\Caminhao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaminhaoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'placa' => ['required', 'string', 'max:10', 'unique:caminhoes,placa'],
            'modelo' => ['required', 'string', 'max:255'],
            'marca' => ['required', 'string', 'max:255'],
            'ano' => ['required', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'capacidade_carga' => ['required', 'numeric', 'min:0'],
            'renavam' => ['nullable', 'string', 'max:20'],
            'cor' => ['nullable', 'string', 'max:50'],
        ];
    }
}
