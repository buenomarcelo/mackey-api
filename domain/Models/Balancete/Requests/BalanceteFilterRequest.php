<?php

namespace MAC\Models\Balancete\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BalanceteFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'caminhao_uuid' => ['nullable', 'uuid', 'exists:caminhoes,uuid'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
        ];
    }
}
