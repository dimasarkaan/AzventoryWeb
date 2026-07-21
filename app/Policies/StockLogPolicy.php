<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\StockLog;
use App\Models\User;

class StockLogPolicy
{
    // Izin: Menentukan siapa yang boleh memantau antrean pengajuan tambah/kurang stok.
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    // Izin: Menentukan siapa yang berhak membuat tiket permohonan restock/pengurangan barang.
    public function create(User $user): bool
    {
        // Semua role (Operator, Admin, Superadmin) bisa mengajukan.
        // Namun Admin akan auto-approved di Controller/Service.
        return true;
    }

    // Izin Manajerial: Hanya mandor gudang (Admin) yang bisa mengetuk palu (Setuju/Tolak) atas permohonan stok.
    public function update(User $user, StockLog $stockLog): bool
    {
        // Hanya Admin yang bisa melakukan approval.
        return $user->role === UserRole::ADMIN;
    }

    // Izin Darurat: Hanya Superadmin yang boleh menghapus catatan jejak digital mutasi stok (Sangat tidak disarankan).
    public function delete(User $user, StockLog $stockLog): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
