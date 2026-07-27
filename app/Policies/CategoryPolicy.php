<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
