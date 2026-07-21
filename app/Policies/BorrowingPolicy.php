<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    // Izin: Menentukan siapa saja yang boleh membuka halaman daftar riwayat peminjaman.
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN, UserRole::OPERATOR]);
    }

    // Izin: Menentukan siapa yang berhak melihat detail spesifik dari sebuah transaksi peminjaman.
    public function view(User $user, Borrowing $borrowing): bool
    {
        // Owner OR Admin/Superadmin
        return $user->id === $borrowing->user_id ||
               in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    // Izin: Menentukan siapa yang diperbolehkan membuat form pengajuan pinjam barang baru.
    public function create(User $user): bool
    {
        // Any authenticated user can borrow (subject to inventory availability)
        return true;
    }

    // Izin: Menentukan siapa yang berhak mengedit data transaksi (contoh: untuk memproses pengembalian barang).
    public function update(User $user, Borrowing $borrowing): bool
    {
        // Owner (for returning) OR Admin/Superadmin
        return $user->id === $borrowing->user_id ||
               in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    // Izin: Menentukan siapa yang memiliki wewenang untuk menghapus log riwayat pinjaman.
    public function delete(User $user, Borrowing $borrowing): bool
    {
        // Only Staff can delete history
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    // Izin: Menentukan siapa yang berhak mengembalikan (restore) data transaksi yang tak sengaja terhapus.
    public function restore(User $user, Borrowing $borrowing): bool
    {
        return in_array($user->role, [UserRole::SUPERADMIN, UserRole::ADMIN]);
    }

    // Izin Hak Asasi: Hanya petinggi tertinggi (Superadmin) yang boleh melenyapkan data transaksi secara permanen.
    public function forceDelete(User $user, Borrowing $borrowing): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }
}
