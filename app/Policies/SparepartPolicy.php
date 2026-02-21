<?php

namespace App\Policies;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SparepartPolicy
{
    /**
     * Tentukan apakah user bisa melihat daftar inventaris.
     */
    public function viewAny(User $user): bool
    {
        return true; // Semua user terautentikasi bisa melihat inventory
    }

    /**
     * Tentukan apakah user bisa melihat detail barang.
     */
    public function view(User $user, Sparepart $sparepart): bool
    {
        return true; // Semua user user terautentikasi bisa melihat detail
    }

    /**
     * Tentukan apakah user bisa menambahkan barang baru.
     */
    public function create(User $user): bool
    {
        // Hanya Superadmin dan Admin yang bisa membuat. Operator TIDAK BISA.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    /**
     * Tentukan apakah user bisa mengubah data barang.
     */
    public function update(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin dan Admin yang bisa update. Operator TIDAK BISA.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    /**
     * Tentukan apakah user bisa menghapus barang (Soft Delete).
     */
    public function delete(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin dan Admin yang bisa delete. Operator TIDAK BISA.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    /**
     * Tentukan apakah user bisa memulihkan barang (Restore).
     */
    public function restore(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }

    /**
     * Tentukan apakah user bisa menghapus permanen barang.
     */
    public function forceDelete(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }
    /**
     * Tentukan apakah user bisa mengubah harga barang.
     */
    public function updatePrice(User $user, Sparepart $sparepart): bool
    {
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }
}
