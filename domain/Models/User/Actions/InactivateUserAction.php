<?php

namespace MAC\Models\User\Actions;

use Illuminate\Validation\ValidationException;
use MAC\Models\User\User;

final class InactivateUserAction
{
    public function handle(User $user): void
    {
        if ($user->is(auth()->user())) {
            throw ValidationException::withMessages([
                'ativo' => ['Você não pode inativar seu próprio usuário.'],
            ]);
        }

        $user->update(['ativo' => false]);
    }
}
