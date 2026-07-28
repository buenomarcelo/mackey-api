<?php

namespace MAC\Models\Caminhao\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use MAC\Base\Support\QuerySort;
use MAC\Models\Caminhao\Caminhao;

final class ListCaminhoesAction
{
    private const array SORTABLE = ['placa', 'modelo', 'marca', 'ano', 'capacidade_carga', 'created_at'];

    public function handle(
        ?string $search = null,
        bool $somenteAtivos = false,
        ?string $sortBy = null,
        bool $descending = false,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Caminhao::query()
            ->with('criadoPor')
            ->when($search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('placa', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%");
            }))
            ->when($somenteAtivos, fn ($query) => $query->where('ativo', true));

        return QuerySort::apply($query, $sortBy, $descending, self::SORTABLE, 'placa')->paginate($perPage);
    }
}
