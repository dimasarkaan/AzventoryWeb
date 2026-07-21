<?php

namespace App\Policies;

use App\Models\Sparepart;
use App\Models\User;

class SparepartPolicy
{
    // Izin: Menentukan siapa saja yang boleh melihat daftar seluruh barang di gudang.
    public function viewAny(User $user): bool
    {
        return true; // Semua user terautentikasi bisa melihat inventory
    }

    // Izin: Menentukan siapa saja yang berhak melihat profil lengkap sebuah barang (stok, foto, deskripsi).
    public function view(User $user, Sparepart $sparepart): bool
    {
        return true; // Semua user user terautentikasi bisa melihat detail
    }

    // Izin: Menentukan siapa yang punya hak untuk menambahkan data barang baru ke dalam sistem.
    public function create(User $user): bool
    {
        // Hanya Superadmin dan Admin yang bisa membuat. Operator TIDAK BISA.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    // Izin: Menentukan siapa yang berhak mengedit nama, deskripsi, atau atribut barang lainnya.
    public function update(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin dan Admin yang bisa update. Operator TIDAK BISA.
        return in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN,
            \App\Enums\UserRole::ADMIN,
        ]);
    }

    // Izin: Menentukan siapa yang boleh membuang barang ke tong sampah (Hapus Sementara).
    public function delete(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin yang bisa delete. Admin dan Operator TIDAK BISA.
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }

    // Izin: Menentukan siapa yang berhak memungut kembali barang dari tong sampah (Pemulihan).
    public function restore(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }

    // Izin Hak Asasi: Menentukan siapa yang memegang kendali penuh untuk memusnahkan data barang secara permanen.
    public function forceDelete(User $user, Sparepart $sparepart): bool
    {
        // Hanya Superadmin
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }

    // Izin Khusus (Keuangan): Hanya penguasa sistem (Superadmin) yang diizinkan mengutak-atik nominal harga barang.
    public function updatePrice(User $user, Sparepart $sparepart): bool
    {
        return $user->role === \App\Enums\UserRole::SUPERADMIN;
    }
}
