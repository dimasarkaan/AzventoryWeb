<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
