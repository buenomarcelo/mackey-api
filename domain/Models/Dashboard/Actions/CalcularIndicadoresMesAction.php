<?php

namespace MAC\Models\Dashboard\Actions;

use Illuminate\Support\Facades\Date;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;

final class CalcularIndicadoresMesAction
{
    public function handle(): array
    {
        $inicioMes = Date::now()->startOfMonth();
        $fimMes = Date::now()->endOfMonth();

        $totalFretes = (float) Viagem::query()
            ->whereBetween('data', [$inicioMes, $fimMes])
            ->where('status_viagem', '!=', StatusViagem::CANCELADA)
            ->sum('frete');

        $totalAbastecimento = (float) Abastecimento::query()
            ->whereBetween('data', [$inicioMes, $fimMes])
            ->sum('valor_enviado');

        $totalDespesas = (float) DespesaCaminhao::query()
            ->whereBetween('data', [$inicioMes, $fimMes])
            ->sum('valor_pago');

        return [
            'total_fretes' => round($totalFretes, 2),
            'total_despesas' => round($totalAbastecimento + $totalDespesas, 2),
            'resultado_liquido' => round($totalFretes - $totalAbastecimento - $totalDespesas, 2),
            'viagens_em_transito' => Viagem::query()->where('status_viagem', StatusViagem::EM_TRANSITO)->count(),
            'viagens_agendadas' => Viagem::query()->where('status_viagem', StatusViagem::AGENDADA)->count(),
        ];
    }
}
