<?php

namespace MAC\Models\Motorista;

use Database\Factories\MotoristaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MAC\Base\Traits\HasUuidRouteKey;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\User\User;
use MAC\Models\Viagem\Viagem;

#[Fillable([
    'nome', 'cpf', 'telefone', 'cnh_numero', 'cnh_categoria', 'cnh_validade',
    'cep', 'logradouro', 'numero', 'cidade', 'uf',
    'percentual_comissao', 'dia_pagamento', 'ativo', 'criado_por_id',
])]
#[Hidden(['id', 'criado_por_id'])]
class Motorista extends Model
{
    /** @use HasFactory<MotoristaFactory> */
    use HasFactory, HasUuids, HasUuidRouteKey {
        HasUuidRouteKey::uniqueIds insteadof HasUuids;
    }

    protected function casts(): array
    {
        return [
            'cnh_validade' => 'date',
            'percentual_comissao' => 'decimal:2',
            'dia_pagamento' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    public function viagens()
    {
        return $this->hasMany(Viagem::class);
    }

    public function contasPagar()
    {
        return $this->hasMany(ContaPagarMotorista::class);
    }

    public function abastecimentos()
    {
        return $this->hasMany(Abastecimento::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por_id');
    }
}
