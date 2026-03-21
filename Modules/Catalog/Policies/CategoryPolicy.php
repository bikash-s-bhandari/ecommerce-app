<?php

namespace Modules\Catalog\Policies;

use Modules\Auth\Enums\UserRoleEnum;
use Modules\Auth\Models\User;
use Modules\Catalog\Models\Category;

class CategoryPolicy
{
    public function create(User $user): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function update(User $user, Category $category): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->role === UserRoleEnum::ADMIN;
    }
}

