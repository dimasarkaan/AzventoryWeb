<?php

namespace App\Policies;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SparepartPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view inventory
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sparepart $sparepart): bool
    {
        return true; // All authenticated users can view details
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Superadmin and Admin can create. Operator CANNOT.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sparepart $sparepart): bool
    {
        // Only Superadmin and Admin can update. Operator CANNOT.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    /**
     * Determine whether the user can delete the model (Soft Delete).
     */
    public function delete(User $user, Sparepart $sparepart): bool
    {
        // Only Superadmin and Admin can delete. Operator CANNOT.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sparepart $sparepart): bool
    {
        // Only Superadmin
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sparepart $sparepart): bool
    {
        // Only Superadmin
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }
}
