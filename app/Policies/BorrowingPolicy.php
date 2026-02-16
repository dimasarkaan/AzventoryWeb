<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;
use App\Enums\UserRole;

class BorrowingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN, UserRole::OPERATOR]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        // Owner OR Staff
        return $user->id === $borrowing->user_id || 
               in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN, UserRole::OPERATOR]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can borrow (subject to inventory availability)
        return true;
    }

    /**
     * Determine whether the user can update the model (e.g., return item).
     */
    public function update(User $user, Borrowing $borrowing): bool
    {
        // Owner (for returning) OR Staff
        return $user->id === $borrowing->user_id || 
               in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN, UserRole::OPERATOR]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Borrowing $borrowing): bool
    {
        // Only Staff can delete history
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Borrowing $borrowing): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Borrowing $borrowing): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
