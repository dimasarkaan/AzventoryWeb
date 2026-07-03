<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    use ActivityLogger;

    /**
     * Memperbarui password pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                Password::min(8)->max(16)->letters()->numbers(),
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (Hash::check($value, auth()->user()->password)) {
                        $fail('Password baru tidak boleh sama dengan password saat ini.');
                    }
                },
            ],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.letters' => 'Kata sandi harus mengandung kombinasi huruf dan angka (8-16 karakter).',
            'password.numbers' => 'Kata sandi harus mengandung kombinasi huruf dan angka (8-16 karakter).',
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->logActivity('Ubah Password', 'User memperbarui kata sandi akun mereka.');

        return back()->with('status', 'password-updated');
    }
}
