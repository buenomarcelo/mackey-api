<?php

namespace MAC\Base\Traits;

/**
 * Pair with Illuminate\Database\Eloquent\Concerns\HasUuids on models that keep
 * an auto-increment `id` as the internal primary key and expose `uuid` externally.
 */
trait HasUuidRouteKey
{
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
