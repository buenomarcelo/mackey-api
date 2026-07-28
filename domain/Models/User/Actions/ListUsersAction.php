<?php

namespace MAC\Models\User\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use MAC\Base\Support\QuerySort;
use MAC\Models\User\User;

final class ListUsersAction
{
    private const array SORTABLE = ['name', 'email', 'created_at'];

    public function handle(
        ?string $search = null,
        ?string $sortBy = null,
        bool $descending = false,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = User::query()
            ->when($search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }));

        return QuerySort::apply($query, $sortBy, $descending, self::SORTABLE, 'name')->paginate($perPage);
    }
}
