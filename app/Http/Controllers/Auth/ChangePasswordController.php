<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use App\Traits\ActivityLogger;

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
     * Memproses penggantian password pengguna.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->max(16)->letters()->numbers(),
            ],
        ];

        // If NOT first login, require current password for security
        if (!is_null($user->password_changed_at)) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        // Only validate username if it's the first login (password_changed_at is null)
        if (is_null($user->password_changed_at)) {
            $rules['username'] = ['required', 'string', 'max:255', 'unique:users,username,' . $user->id];
        }

        $request->validate($rules);

        $updateData = [
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ];

        // Only update username on first login
        if (is_null($user->password_changed_at)) {
            $updateData['username'] = $request->username;
        }

        $user->update($updateData);

        $this->logActivity('Ganti Password', "User mengubah password mereka.");

        // Redirect based on role after successful password change
        $redirectPath = match ($user->role) {
            \App\Enums\UserRole::SUPERADMIN => route('dashboard.superadmin'),
            \App\Enums\UserRole::ADMIN => route('dashboard.admin'),
            \App\Enums\UserRole::OPERATOR => route('dashboard.operator'),
            default => route('dashboard'),
        };

        return redirect($redirectPath)->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
