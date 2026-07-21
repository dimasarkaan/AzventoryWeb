<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    // Izin Akses: Hanya penguasa sistem (Superadmin) yang boleh melihat daftar seluruh akun karyawan.
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    // Izin Akses: User hanya boleh melihat profilnya sendiri. Sisanya adalah hak mutlak Superadmin.
    public function view(User $user, User $model): bool
    {
        return $user->role === UserRole::SUPERADMIN || $user->id === $model->id;
    }

    // Izin Akses: Hanya staf HRD/Superadmin yang berwenang membuatkan akun untuk pegawai baru.
    public function create(User $user): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    // Izin Akses: Superadmin memiliki kebebasan penuh untuk mengedit data pengguna mana pun.
    public function update(User $user, ?User $model = null): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    // Izin Akses: Otoritas Superadmin untuk memberhentikan/menonaktifkan akun karyawan (Soft Delete).
    public function delete(User $user, ?User $model = null): bool
    {
        // Jika model null (misal: cek otoritas umum), izinkan jika superadmin
        if (! $model) {
            return $user->role === UserRole::SUPERADMIN;
        }

        // Mencegah penghapusan diri sendiri
        return $user->role === UserRole::SUPERADMIN && $user->id !== $model->id;
    }

    // Izin Akses: Wewenang untuk mengaktifkan kembali akun karyawan yang sebelumnya telah diberhentikan.
    public function restore(User $user, ?User $model = null): bool
    {
        return $user->role === UserRole::SUPERADMIN;
    }

    // Hak Asasi Tertinggi: Superadmin dapat menghapus akun secara permanen. Tapi sistem melarang "bunuh diri" (menghapus akun sendiri).
    public function forceDelete(User $user, ?User $model = null): bool
    {
        if (! $model) {
            return $user->role === UserRole::SUPERADMIN;
        }

        return $user->role === UserRole::SUPERADMIN && $user->id !== $model->id;
    }
}
