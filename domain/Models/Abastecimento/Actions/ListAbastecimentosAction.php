<?php

namespace MAC\Models\Abastecimento\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use MAC\Base\Support\QuerySort;
use MAC\Models\Abastecimento\Abastecimento;

final class ListAbastecimentosAction
{
    private const array SORTABLE = ['data', 'km', 'litragem', 'valor_litro', 'valor_enviado', 'valor_sobrando', 'posto'];

    public function handle(
        ?string $motoristaUuid = null,
        ?string $caminhaoUuid = null,
        ?string $data = null,
        ?string $sortBy = null,
        bool $descending = false,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Abastecimento::query()
            ->with(['motorista', 'caminhao', 'criadoPor'])
            ->when($motoristaUuid, fn ($query, $uuid) => $query->whereHas('motorista', fn ($q) => $q->where('uuid', $uuid)))
            ->when($caminhaoUuid, fn ($query, $uuid) => $query->whereHas('caminhao', fn ($q) => $q->where('uuid', $uuid)))
            ->when($data, fn ($query, $d) => $query->whereDate('data', '=', $d));

        return QuerySort::apply($query, $sortBy, $descending, self::SORTABLE, 'data')->paginate($perPage);
    }
}
