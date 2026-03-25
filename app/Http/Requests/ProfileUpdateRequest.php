<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Persiapkan data untuk validasi.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => strtolower($this->username),
            ]);
        }
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|08)[0-9]{8,13}$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'], // Max 10MB (Optimized on save)
        ];

        if (! $this->user()->is_username_changed) {
            $rules['username'] = ['required', 'string', 'lowercase', 'min:3', 'max:255', 'regex:/^[a-zA-Z0-9._]+$/', Rule::unique(User::class)->ignore($this->user()->id)];
        }

        return $rules;
    }

    /**
     * Dapatkan pesan kustom untuk aturan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Format Username tidak valid. Hanya boleh berisi huruf kecil, angka, titik, atau garis bawah (_).',
            'username.min' => 'Username harus minimal 3 karakter.',
            'phone.regex' => 'Format Nomor WhatsApp tidak valid. Gunakan format Indonesia (misal: 08... atau +62...).',
        ];
    }
}
