<?php

namespace MAC\Models\Auth\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use MAC\Models\User\User;

final class LoginAction
{
    public function handle(Request $request, string $email, string $password): User
    {
        if (! Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::guard('web')->user();

        return $user;
    }
}
