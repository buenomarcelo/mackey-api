<?php

namespace MAC\Models\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use MAC\Models\User\Actions\CreateUserAction;
use MAC\Models\User\Actions\InactivateUserAction;
use MAC\Models\User\Actions\ListUsersAction;
use MAC\Models\User\Actions\UpdateUserAction;
use MAC\Models\User\DTO\UserData;
use MAC\Models\User\Requests\StoreUserRequest;
use MAC\Models\User\Requests\UpdateUserRequest;
use MAC\Models\User\Resources\UserResource;
use MAC\Models\User\User;

class UserController extends Controller
{
    public function index(Request $request, ListUsersAction $action): AnonymousResourceCollection
    {
        $users = $action->handle(
            search: $request->string('search')->toString() ?: null,
            sortBy: $request->string('sort_by')->toString() ?: null,
            descending: $request->boolean('descending'),
            perPage: $request->integer('per_page', 15),
        );

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request, CreateUserAction $action): UserResource
    {
        $user = $action->handle(UserData::fromRequest($request));

        return new UserResource($user);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): UserResource
    {
        $user = $action->handle($user, UserData::fromRequest($request));

        return new UserResource($user);
    }

    public function destroy(User $user, InactivateUserAction $action): Response
    {
        $action->handle($user);

        return response()->noContent();
    }
}
