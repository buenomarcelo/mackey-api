<?php

namespace MAC\Base\Support;

use Illuminate\Database\Eloquent\Builder;

final class QuerySort
{
    /**
     * Applies an ORDER BY validated against a whitelist of sortable columns,
     * falling back to $defaultColumn when $sortBy is missing or not allowed.
     *
     * @param  array<int, string>  $allowed
     */
    public static function apply(Builder $query, ?string $sortBy, bool $descending, array $allowed, string $defaultColumn): Builder
    {
        $column = ($sortBy && in_array($sortBy, $allowed, true)) ? $sortBy : $defaultColumn;

        return $query->orderBy($column, $descending ? 'desc' : 'asc');
    }
}
