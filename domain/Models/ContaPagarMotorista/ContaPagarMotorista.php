<?php

namespace MAC\Models\ContaPagarMotorista;

use Carbon\Carbon;
use Database\Factories\ContaPagarMotoristaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MAC\Base\Traits\HasUuidRouteKey;
use MAC\Models\ContaPagarMotorista\Enums\StatusContaPagar;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;
use MAC\Models\Viagem\Viagem;

#[Fillable(['motorista_id', 'viagem_id', 'valor_comissao', 'status', 'data_pagamento', 'criado_por_id'])]
#[Hidden(['id', 'motorista_id', 'viagem_id', 'criado_por_id'])]
class ContaPagarMotorista extends Model
{
    /** @use HasFactory<ContaPagarMotoristaFactory> */
    use HasFactory, HasUuids, HasUuidRouteKey {
        HasUuidRouteKey::uniqueIds insteadof HasUuids;
    }

    protected $table = 'contas_pagar_motorista';

    protected function casts(): array
    {
        return [
            'valor_comissao' => 'decimal:2',
            'status' => StatusContaPagar::class,
            'data_pagamento' => 'date',
        ];
    }

    public function motorista()
    {
        return $this->belongsTo(Motorista::class);
    }

    public function viagem()
    {
        return $this->belongsTo(Viagem::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por_id');
    }

    /**
     * Data em que a comissão deveria ter sido paga: a próxima ocorrência do
     * "dia de pagamento" do motorista a partir da data em que a conta foi gerada.
     */
    public function getDataVencimentoAttribute(): Carbon
    {
        $diaPagamento = $this->motorista->dia_pagamento;
        $criadoEm = $this->created_at->copy()->startOfDay();

        $vencimento = $criadoEm->copy()->day(min($diaPagamento, $criadoEm->daysInMonth));

        if ($vencimento->lt($criadoEm)) {
            $vencimento->addMonthNoOverflow();
            $vencimento->day(min($diaPagamento, $vencimento->daysInMonth));
        }

        return $vencimento;
    }

    public function getVencidoAttribute(): bool
    {
        if ($this->status === StatusContaPagar::PAGO) {
            return false;
        }

        return now()->startOfDay()->gt($this->data_vencimento);
    }
}
