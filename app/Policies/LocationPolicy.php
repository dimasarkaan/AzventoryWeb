<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    public function update(User $user, Location $location): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    public function delete(User $user, Location $location): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
