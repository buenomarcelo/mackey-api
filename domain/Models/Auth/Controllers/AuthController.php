<?php

namespace MAC\Models\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MAC\Models\Auth\Actions\LoginAction;
use MAC\Models\Auth\Actions\LogoutAction;
use MAC\Models\Auth\Requests\LoginRequest;
use MAC\Models\User\Resources\UserResource;

class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginAction $action): UserResource
    {
        $user = $action->handle(
            request: $request,
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
        );

        return new UserResource($user);
    }

    public function logout(Request $request, LogoutAction $action): Response
    {
        $action->handle($request);

        return response()->noContent();
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
