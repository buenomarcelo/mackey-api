<?php

namespace MAC\Models\User\Actions;

use MAC\Models\User\DTO\UserData;
use MAC\Models\User\User;

final class CreateUserAction
{
    public function handle(UserData $data): User
    {
        return User::create($data->toArray());
    }
}
