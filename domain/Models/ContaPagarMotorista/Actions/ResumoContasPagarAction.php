<?php

namespace MAC\Models\ContaPagarMotorista\Actions;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\ContaPagarMotorista\Enums\StatusContaPagar;

final class ResumoContasPagarAction
{
    /**
     * Agrupa as comissões pendentes por motorista, considerando o mês em que
     * cada uma vence (data_vencimento é calculada a partir do dia_pagamento
     * do motorista, não é uma coluna — por isso o agrupamento é feito aqui,
     * em PHP, em vez de uma consulta agregada no banco).
     */
    public function handle(int $ano, int $mes): array
    {
        $mesAlvo = Carbon::create($ano, $mes, 1)->startOfMonth();

        // A data de vencimento nunca passa de ~1 mês após a criação, então
        // já reduzimos o conjunto candidato pela data de criação.
        $contas = ContaPagarMotorista::query()
            ->with('motorista')
            ->where('status', StatusContaPagar::PENDENTE)
            ->whereBetween('created_at', [
                $mesAlvo->copy()->subMonthNoOverflow(),
                $mesAlvo->copy()->endOfMonth(),
            ])
            ->get()
            ->filter(fn (ContaPagarMotorista $conta) => $conta->data_vencimento->isSameMonth($mesAlvo));

        return $contas
            ->groupBy('motorista_id')
            ->map(function (Collection $contasDoMotorista) {
                $motorista = $contasDoMotorista->first()->motorista;

                return [
                    'motorista_uuid' => $motorista->uuid,
                    'motorista_nome' => $motorista->nome,
                    'total_pendente' => round((float) $contasDoMotorista->sum('valor_comissao'), 2),
                    'quantidade' => $contasDoMotorista->count(),
                ];
            })
            ->sortByDesc('total_pendente')
            ->values()
            ->all();
    }
}
