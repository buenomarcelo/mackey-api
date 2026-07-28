<?php

namespace MAC\Models\DespesaCaminhao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDespesaCaminhaoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'caminhao_uuid' => ['required', 'uuid', 'exists:caminhoes,uuid'],
            'servico' => ['required', 'string'],
            'valor_pago' => ['required', 'numeric', 'min:0'],
            'data_despesa' => ['required', 'date'],
        ];
    }
}
