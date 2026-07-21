<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    // Tentukan izin akses (selalu true karena dilindungi route/middleware).
    public function authorize(): bool
    {
        return true;
    }

    // Syarat wajib yang harus dipatuhi saat mendaftarkan akun pengguna baru ke sistem
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z][a-zA-Z\s\.\'\-]*$/'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users'],
            'role' => ['required', 'in:superadmin,admin,operator'],
            'jabatan' => ['required', 'string', 'min:3', 'max:255', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'"]*$/'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }

    // Memberikan nama samaran (Alias) agar pesan error yang muncul menggunakan kata yang lebih sopan (contoh: 'name' jadi 'Nama Lengkap')
    public function attributes(): array
    {
        return [
            'name' => 'Nama Lengkap',
            'email' => 'Alamat Email',
            'role' => 'Peran',
            'jabatan' => 'Jabatan',
            'status' => 'Status Akun',
        ];
    }

    // Mengubah pesan error bawaan pabrik menjadi bahasa Indonesia yang mudah dipahami (POV Manusia)
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal harus 3 karakter.',
            'name.regex' => 'Nama lengkap harus diawali huruf dan tidak boleh mengandung angka atau simbol khusus (kecuali titik, petik, atau strip).',
            
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid. Pastikan menggunakan @ dan nama domain yang benar (contoh: nama@domain.com).',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan gunakan email lain.',
            
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Pilihan peran tidak valid. Harus superadmin, admin, atau operator.',
            
            'jabatan.required' => 'Jabatan wajib diisi.',
            'jabatan.min' => 'Jabatan minimal harus 3 karakter.',
            'jabatan.regex' => 'Jabatan harus mengandung huruf, diawali huruf/angka, serta hanya berisi huruf/angka/spasi/simbol (.,&-()/\'").',
            
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Pilihan status tidak valid.',
        ];
    }
}
