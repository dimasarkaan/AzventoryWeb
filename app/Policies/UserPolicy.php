<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Access\Response;

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
    public function update(User $user, User $model): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    /**
     * Tentukan apakah user bisa menghapus pengguna.
     */
    public function delete(User $user, User $model): bool
    {
        // Mencegah penghapusan diri sendiri, juga ditangani di controller tetapi kebijakan ini lebih aman.
        return $user->role === UserRole::SUPERADMIN && $user->id !== $model->id;
    }

    /**
     * Tentukan apakah user bisa memulihkan pengguna (restore).
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    /**
     * Tentukan apakah user bisa menghapus permanen pengguna.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === UserRole::SUPERADMIN && $user->id !== $model->id;
    }
}
