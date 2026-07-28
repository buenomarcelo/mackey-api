<?php

namespace MAC\Models\ContaPagarMotorista\Actions;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use MAC\Base\Support\QuerySort;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;

final class ListContasPagarAction
{
    private const array SORTABLE = ['valor_comissao', 'status', 'data_pagamento', 'created_at'];

    public function handle(
        ?string $motoristaUuid = null,
        ?string $status = null,
        ?string $dataInicio = null,
        ?string $dataFim = null,
        ?string $dataVencimento = null,
        ?string $sortBy = null,
        bool $descending = false,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = ContaPagarMotorista::query()
            ->with(['motorista', 'viagem', 'criadoPor'])
            ->when($motoristaUuid, fn ($query, $uuid) => $query->whereHas('motorista', fn ($q) => $q->where('uuid', $uuid)))
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($dataInicio, fn ($query, $data) => $query->whereDate('created_at', '>=', $data))
            ->when($dataFim, fn ($query, $data) => $query->whereDate('created_at', '<=', $data));

        // data_vencimento é calculada em PHP (depende do dia_pagamento do
        // motorista), não existe como coluna — filtrar e paginar em memória.
        if ($dataVencimento) {
            return $this->paginateFilteredByVencimento($query, $dataVencimento, $perPage);
        }

        return QuerySort::apply($query, $sortBy, $descending, self::SORTABLE, 'created_at')->paginate($perPage);
    }

    private function paginateFilteredByVencimento(Builder $query, string $dataVencimento, int $perPage): LengthAwarePaginator
    {
        $alvo = Carbon::parse($dataVencimento)->startOfDay();

        $items = $query->orderByDesc('created_at')->get()
            ->filter(fn (ContaPagarMotorista $conta) => $conta->data_vencimento->isSameDay($alvo))
            ->values();

        $page = Paginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()],
        );
    }
}
