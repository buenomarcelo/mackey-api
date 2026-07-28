<?php

namespace MAC\Models\DespesaCaminhao\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use MAC\Base\Support\QuerySort;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;

final class ListDespesasCaminhaoAction
{
    private const array SORTABLE = ['data', 'servico', 'valor_pago'];

    public function handle(
        ?string $caminhaoUuid = null,
        ?string $data = null,
        ?string $sortBy = null,
        bool $descending = false,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = DespesaCaminhao::query()
            ->with(['caminhao', 'criadoPor'])
            ->when($caminhaoUuid, fn ($query, $uuid) => $query->whereHas('caminhao', fn ($q) => $q->where('uuid', $uuid)))
            ->when($data, fn ($query, $d) => $query->whereDate('data', '=', $d));

        return QuerySort::apply($query, $sortBy, $descending, self::SORTABLE, 'data')->paginate($perPage);
    }
}
