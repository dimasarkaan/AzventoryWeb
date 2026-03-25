<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Models\User;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    use ActivityLogger;

    /**
     * Menampilkan halaman ganti password.
     */
    public function create()
    {
        return view('auth.change-password');
    }

    /**
     * Memproses penggantian password pengguna (Aktivasi Pertama).
     */
    public function store(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $updateData = [
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ];

        // Update username hanya jika ini adalah login pertama (aktivasi)
        if (is_null($user->password_changed_at) && $request->filled('username')) {
            $updateData['username'] = $request->username;
            // Catatan: Tidak mengubah is_username_changed menjadi true di sini,
            // sesuai permintaan user agar jatah 1x ganti di Profil tetap ada.
        }

        $user->update($updateData);

        $this->logActivity('Ganti Password', 'User mengubah password (dan username) saat aktivasi pertama.');

        // Redirect berdasarkan role setelah ganti password berhasil
        $redirectPath = match ($user->role) {
            \App\Enums\UserRole::SUPERADMIN => route('dashboard.superadmin'),
            \App\Enums\UserRole::ADMIN => route('dashboard.admin'),
            \App\Enums\UserRole::OPERATOR => route('dashboard.operator'),
            default => route('dashboard'),
        };

        return redirect($redirectPath)->with('success', 'Akun berhasil diaktifkan. Selamat datang!');
    }
}
