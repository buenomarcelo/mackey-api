<?php

namespace MAC\Models\Motorista\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use MAC\Base\Support\QuerySort;
use MAC\Models\Motorista\Motorista;

final class ListMotoristasAction
{
    private const array SORTABLE = ['nome', 'cpf', 'telefone', 'percentual_comissao', 'dia_pagamento', 'created_at'];

    public function handle(
        ?string $search = null,
        bool $somenteAtivos = false,
        ?string $sortBy = null,
        bool $descending = false,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Motorista::query()
            ->with('criadoPor')
            ->when($search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            }))
            ->when($somenteAtivos, fn ($query) => $query->where('ativo', true));

        return QuerySort::apply($query, $sortBy, $descending, self::SORTABLE, 'nome')->paginate($perPage);
    }
}
