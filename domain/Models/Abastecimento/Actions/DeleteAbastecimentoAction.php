<?php

namespace MAC\Models\Abastecimento\Actions;

use MAC\Models\Abastecimento\Abastecimento;

final class DeleteAbastecimentoAction
{
    public function handle(Abastecimento $abastecimento): void
    {
        $abastecimento->delete();
    }
}
