<?php

namespace MAC\Models\Viagem\Actions;

use MAC\Models\Viagem\Viagem;

final class DeleteViagemAction
{
    public function handle(Viagem $viagem): void
    {
        $viagem->delete();
    }
}
