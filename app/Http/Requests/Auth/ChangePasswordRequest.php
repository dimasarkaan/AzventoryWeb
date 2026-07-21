<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    // Menentukan siapa saja yang boleh memproses form ini (Disetel 'true' karena semua berhak mengganti password)
    public function authorize(): bool
    {
        return true;
    }

    // Membersihkan data username dari spasi kosong dan huruf besar sebelum masuk database
    protected function prepareForValidation(): void
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => strtolower(str_replace(' ', '', $this->username)),
            ]);
        }
    }

    // Menentukan seberapa ketat kombinasi password baru (Wajib 8-16 karakter, ada angka, ada huruf)
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
            $rules['password'][] = 'different:current_password';
        } else {
            // Pada first login (password_changed_at null), form tidak mengirim current_password
            // Maka kita gunakan Hash::check untuk memastikan password baru tidak sama dengan password default
            $rules['password'][] = function ($attribute, $value, $fail) use ($user) {
                if (\Illuminate\Support\Facades\Hash::check($value, $user->password)) {
                    $fail('Kata sandi baru harus berbeda dengan kata sandi saat ini.');
                }
            };
        }

        // Hanya validasi username pada saat aktivasi pertama
        if (is_null($user->password_changed_at)) {
            $rules['username'] = [
                'required',
                'string',
                'lowercase',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9][a-zA-Z0-9._]*$/',
                'unique:users,username,'.$user->id,
            ];
        }

        return $rules;
    }

    // Daftar pesan peringatan jika pengguna melanggar aturan di atas
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
            'password.max' => 'Kata sandi maksimal 16 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.letters' => 'Kata sandi harus mengandung kombinasi huruf dan angka (8-16 karakter).',
            'password.numbers' => 'Kata sandi harus mengandung kombinasi huruf dan angka (8-16 karakter).',
            'password.different' => 'Kata sandi baru harus berbeda dengan kata sandi saat ini.',
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
        ];
    }
}
