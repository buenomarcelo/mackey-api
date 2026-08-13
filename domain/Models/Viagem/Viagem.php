<?php

namespace MAC\Models\Viagem;

use Database\Factories\ViagemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MAC\Base\Traits\HasUuidRouteKey;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use MAC\Models\Viagem\Enums\StatusPagamento;
use MAC\Models\Viagem\Enums\StatusViagem;

#[Fillable([
    'motorista_id', 'caminhao_id', 'data', 'origem', 'destino', 'contrato',
    'peso', 'frete', 'entrada', 'valor_2', 'valor_3', 'pedagio', 'valor_adicional', 'motivo',
    'status_viagem', 'status_pagamento', 'criado_por_id',
])]
#[Hidden(['id', 'motorista_id', 'caminhao_id', 'criado_por_id'])]
class Viagem extends Model
{
    /** @use HasFactory<ViagemFactory> */
    use HasFactory, HasUuids, HasUuidRouteKey {
        HasUuidRouteKey::uniqueIds insteadof HasUuids;
    }

    protected $table = 'viagens';

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'peso' => 'decimal:2',
            'frete' => 'decimal:2',
            'entrada' => 'decimal:2',
            'valor_2' => 'decimal:2',
            'valor_3' => 'decimal:2',
            'pedagio' => 'decimal:2',
            'valor_adicional' => 'decimal:2',
            'status_viagem' => StatusViagem::class,
            'status_pagamento' => StatusPagamento::class,
        ];
    }

    public function getRestanteAttribute(): float
    {
        return (float) $this->frete - (float) $this->entrada - (float) $this->valor_2 - (float) $this->valor_3;
    }

    public function motorista()
    {
        return $this->belongsTo(Motorista::class);
    }

    public function caminhao()
    {
        return $this->belongsTo(Caminhao::class);
    }

    public function contaPagar()
    {
        return $this->hasOne(ContaPagarMotorista::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por_id');
    }
}
