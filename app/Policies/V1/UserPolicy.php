<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Permission\V1\Abilities;

class UserPolicy
{
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateUser);
    }

    public function update(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::UpdateUser);
    }

    public function replace(User $user): bool
    {
        return $user->tokenCan(Abilities::ReplaceUser);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::DeleteUser);
    }
}
