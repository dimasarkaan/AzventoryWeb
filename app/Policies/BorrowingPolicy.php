<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;
use App\Enums\UserRole;

class BorrowingPolicy
{
    /**
     * Tentukan apakah user bisa melihat daftar peminjaman.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN, UserRole::OPERATOR]);
    }

    /**
     * Tentukan apakah user bisa melihat detail peminjaman.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        // Owner OR Staff
        return $user->id === $borrowing->user_id || 
               in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN, UserRole::OPERATOR]);
    }

    /**
     * Tentukan apakah user bisa mengajukan peminjaman.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can borrow (subject to inventory availability)
        return true;
    }

    /**
     * Tentukan apakah user bisa mengubah data peminjaman (misal: pengembalian).
     */
    public function update(User $user, Borrowing $borrowing): bool
    {
        // Owner (for returning) OR Staff
        return $user->id === $borrowing->user_id || 
               in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN, UserRole::OPERATOR]);
    }

    /**
     * Tentukan apakah user bisa menghapus data peminjaman.
     */
    public function delete(User $user, Borrowing $borrowing): bool
    {
        // Only Staff can delete history
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    /**
     * Tentukan apakah user bisa memulihkan data peminjaman yang dihapus.
     */
    public function restore(User $user, Borrowing $borrowing): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    /**
     * Tentukan apakah user bisa menghapus permanen data peminjaman.
     */
    public function forceDelete(User $user, Borrowing $borrowing): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
