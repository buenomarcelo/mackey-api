<?php

namespace MAC\Models\User\Actions;

use Illuminate\Validation\ValidationException;
use MAC\Models\User\DTO\UserData;
use MAC\Models\User\User;

final class UpdateUserAction
{
    public function handle(User $user, UserData $data): User
    {
        if ($user->is(auth()->user()) && ! $data->isAdmin) {
            throw ValidationException::withMessages([
                'is_admin' => ['Você não pode remover seu próprio acesso de administrador.'],
            ]);
        }

        $user->update($data->toArray());

        // The auth:sanctum middleware calls Auth::shouldUse('sanctum'), so the
        // default guard here is no longer 'web'. Sanctum's session-authentication
        // middleware checks the 'web' guard specifically (see sanctum.guard),
        // so that's the one we must refresh with the new password hash to avoid
        // force-logging the user out on their very next request.
        if ($user->is(auth('web')->user())) {
            auth('web')->setUser($user);
        }

        return $user;
    }
}
