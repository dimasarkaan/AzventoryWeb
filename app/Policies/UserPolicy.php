<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    /**
     * Tentukan apakah user bisa melihat daftar pengguna.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    /**
     * Tentukan apakah user bisa melihat detail pengguna.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === UserRole::SUPERADMIN || $user->id === $model->id;
    }

    /**
     * Tentukan apakah user bisa membuat pengguna baru.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    /**
     * Tentukan apakah user bisa mengubah data pengguna.
     */
    public function update(User $user, ?User $model = null): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    /**
     * Tentukan apakah user bisa menghapus pengguna.
     */
    public function delete(User $user, ?User $model = null): bool
    {
        // Jika model null (misal: cek otoritas umum), izinkan jika superadmin
        if (! $model) {
            return $user->role === UserRole::SUPERADMIN;
        }

        // Mencegah penghapusan diri sendiri
        return $user->role === UserRole::SUPERADMIN && $user->id !== $model->id;
    }

    /**
     * Tentukan apakah user bisa memulihkan pengguna (restore).
     */
    public function restore(User $user, ?User $model = null): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    /**
     * Tentukan apakah user bisa menghapus permanen pengguna.
     */
    public function forceDelete(User $user, ?User $model = null): bool
    {
        if (! $model) {
            return $user->role === UserRole::SUPERADMIN;
        }

        return $user->role === UserRole::SUPERADMIN && $user->id !== $model->id;
    }
}
