<?php

namespace MAC\Models\Motorista\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMotoristaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:20', 'unique:motoristas,cpf'],
            'telefone' => ['required', 'string', 'max:20'],
            'cnh_numero' => ['required', 'string', 'max:20'],
            'cnh_categoria' => ['required', 'string', 'max:5'],
            'cnh_validade' => ['required', 'date'],
            'cep' => ['nullable', 'string', 'max:10'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'size:2'],
            'percentual_comissao' => ['required', 'numeric', 'min:0', 'max:100'],
            'dia_pagamento' => ['required', 'integer', 'min:1', 'max:31'],
        ];
    }
}
