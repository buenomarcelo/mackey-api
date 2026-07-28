<?php

namespace MAC\Models\Viagem\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use MAC\Base\Support\QuerySort;
use MAC\Models\Viagem\Viagem;

final class ListViagensAction
{
    private const array SORTABLE = ['data', 'origem', 'destino', 'contrato', 'peso', 'frete', 'status_viagem', 'status_pagamento'];

    public function handle(
        ?string $motoristaUuid = null,
        ?string $caminhaoUuid = null,
        ?string $statusViagem = null,
        ?string $statusPagamento = null,
        ?string $dataInicio = null,
        ?string $dataFim = null,
        ?string $sortBy = null,
        bool $descending = false,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Viagem::query()
            ->with(['motorista', 'caminhao', 'criadoPor'])
            ->when($motoristaUuid, fn ($query, $uuid) => $query->whereHas('motorista', fn ($q) => $q->where('uuid', $uuid)))
            ->when($caminhaoUuid, fn ($query, $uuid) => $query->whereHas('caminhao', fn ($q) => $q->where('uuid', $uuid)))
            ->when($statusViagem, fn ($query, $status) => $query->where('status_viagem', $status))
            ->when($statusPagamento, fn ($query, $status) => $query->where('status_pagamento', $status))
            ->when($dataInicio, fn ($query, $data) => $query->whereDate('data', '>=', $data))
            ->when($dataFim, fn ($query, $data) => $query->whereDate('data', '<=', $data));

        if ($sortBy === 'restante') {
            $query->orderByRaw('(frete - entrada - valor_2 - valor_3) '.($descending ? 'desc' : 'asc'));
        } else {
            QuerySort::apply($query, $sortBy, $descending, self::SORTABLE, 'data');
        }

        return $query->paginate($perPage);
    }
}
