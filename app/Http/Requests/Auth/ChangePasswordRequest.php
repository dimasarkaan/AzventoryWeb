<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sempurnakan data sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => strtolower(str_replace(' ', '', $this->username)),
            ]);
        }
    }

    /**
     * Aturan validasi input.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->max(16)->letters()->numbers(),
            ],
        ];

        // Jika bukan login pertama, wajibkan password lama
        if (! is_null($user->password_changed_at)) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        // Hanya validasi username pada saat aktivasi pertama
        if (is_null($user->password_changed_at)) {
            $rules['username'] = [
                'required',
                'string',
                'lowercase',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9._]+$/',
                'unique:users,username,'.$user->id,
            ];
        }

        return $rules;
    }

    /**
     * Pesan error kustom dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.lowercase' => 'Username harus menggunakan huruf kecil semua.',
            'username.min' => 'Username harus minimal 3 karakter.',
            'username.regex' => 'Format Username tidak valid. Hanya boleh berisi huruf kecil, angka, titik (.), atau garis bawah (_). Tanpa spasi.',
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password' => 'Kata sandi harus mengandung kombinasi huruf dan angka (8-16 karakter).',
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
        ];
    }
}
