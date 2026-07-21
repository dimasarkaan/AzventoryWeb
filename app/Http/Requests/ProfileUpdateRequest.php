<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    // Tahap Persiapan: Mencegat dan mengubah data SEBELUM divalidasi
    // (Contoh: memaksa username menjadi huruf kecil semua agar seragam)
    protected function prepareForValidation(): void
    {
        if ($this->has('username')) {
            $this->merge([
                'username' => strtolower($this->username),
            ]);
        }
    }

    // Menentukan syarat/aturan main yang harus dipenuhi oleh form profil
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z][a-zA-Z\s\.\'\-]*$/'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|08)[0-9]{8,13}$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'], // Max 10MB (Optimized on save)
        ];

        if (! $this->user()->is_username_changed) {
            $rules['username'] = ['required', 'string', 'lowercase', 'min:3', 'max:255', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9._]*$/', Rule::unique(User::class)->ignore($this->user()->id)];
        }

        return $rules;
    }

    // Menerjemahkan pesan error bawaan sistem menjadi bahasa Indonesia yang ramah (POV Manusia)
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal harus 3 karakter.',
            'name.regex' => 'Nama lengkap harus diawali huruf dan tidak boleh mengandung angka atau simbol khusus (kecuali titik, petik, atau strip).',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid. Pastikan menggunakan @ dan nama domain yang benar (contoh: nama@domain.com).',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username harus minimal 3 karakter.',
            'username.regex' => 'Format Username tidak valid. Hanya boleh berisi huruf kecil, angka, titik, atau garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan.',
            'phone.regex' => 'Format Nomor WhatsApp tidak valid. Gunakan format Indonesia (misal: 08... atau +62...).',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'avatar.max' => 'Ukuran gambar maksimal adalah 10MB.',
        ];
    }
}
