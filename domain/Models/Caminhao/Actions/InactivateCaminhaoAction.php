<?php

namespace MAC\Models\Caminhao\Actions;

use MAC\Models\Caminhao\Caminhao;

final class InactivateCaminhaoAction
{
    public function handle(Caminhao $caminhao): void
    {
        $caminhao->update(['ativo' => false]);
    }
}
