<?php

namespace MAC\Models\DespesaCaminhao\Actions;

use MAC\Models\DespesaCaminhao\DespesaCaminhao;

final class DeleteDespesaCaminhaoAction
{
    public function handle(DespesaCaminhao $despesa): void
    {
        $despesa->delete();
    }
}
