<?php

namespace MAC\Models\Balancete\Actions;

use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\Viagem\Enums\StatusViagem;
use MAC\Models\Viagem\Viagem;

final class CalcularBalanceteAction
{
    public function handle(?string $caminhaoUuid = null, ?string $dataInicio = null, ?string $dataFim = null): array
    {
        $caminhaoId = $caminhaoUuid
            ? Caminhao::query()->where('uuid', $caminhaoUuid)->value('id')
            : null;

        $totalFretes = (float) Viagem::query()
            ->when($caminhaoId, fn ($query) => $query->where('caminhao_id', $caminhaoId))
            ->when($dataInicio, fn ($query, $data) => $query->whereDate('data', '>=', $data))
            ->when($dataFim, fn ($query, $data) => $query->whereDate('data', '<=', $data))
            ->where('status_viagem', '!=', StatusViagem::CANCELADA)
            ->sum('frete');

        $totalAbastecimento = (float) Abastecimento::query()
            ->when($caminhaoId, fn ($query) => $query->where('caminhao_id', $caminhaoId))
            ->when($dataInicio, fn ($query, $data) => $query->whereDate('data', '>=', $data))
            ->when($dataFim, fn ($query, $data) => $query->whereDate('data', '<=', $data))
            ->sum('valor_enviado');

        $totalDespesas = (float) DespesaCaminhao::query()
            ->when($caminhaoId, fn ($query) => $query->where('caminhao_id', $caminhaoId))
            ->when($dataInicio, fn ($query, $data) => $query->whereDate('data', '>=', $data))
            ->when($dataFim, fn ($query, $data) => $query->whereDate('data', '<=', $data))
            ->sum('valor_pago');

        return [
            'total_fretes' => round($totalFretes, 2),
            'total_abastecimento' => round($totalAbastecimento, 2),
            'total_despesas' => round($totalDespesas, 2),
            'resultado_liquido' => round($totalFretes - $totalAbastecimento - $totalDespesas, 2),
        ];
    }
}
