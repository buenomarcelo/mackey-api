<?php

namespace MAC\Models\Dashboard\Actions;

use Illuminate\Database\Eloquent\Collection;
use MAC\Models\Viagem\Enums\StatusPagamento;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;

final class ViagensPagamentoPendenteAction
{
    public function handle(): Collection
    {
        return Viagem::query()
            ->with(['motorista', 'caminhao'])
            ->where('status_viagem', StatusViagem::FINALIZADA)
            ->where('status_pagamento', '!=', StatusPagamento::PAGO)
            ->orderBy('data')
            ->limit(20)
            ->get();
    }
}
