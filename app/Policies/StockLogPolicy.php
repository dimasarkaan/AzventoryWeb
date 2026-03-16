<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\StockLog;
use App\Models\User;

class StockLogPolicy
{
    /**
     * Tentukan apakah user bisa melihat daftar pengajuan stok.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    /**
     * Tentukan apakah user bisa mengajukan perubahan stok.
     */
    public function create(User $user): bool
    {
        // Semua role (Operator, Admin, Superadmin) bisa mengajukan.
        // Namun Admin/Superadmin akan auto-approved di Controller/Service.
        return true;
    }

    /**
     * Tentukan apakah user bisa menyetujui/menolak pengajuan stok.
     */
    public function update(User $user, StockLog $stockLog): bool
    {
        // Hanya Admin dan Superadmin yang bisa melakukan approval.
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    /**
     * Tentukan apakah user bisa menghapus log stok (biasanya jarang dilakukan).
     */
    public function delete(User $user, StockLog $stockLog): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
