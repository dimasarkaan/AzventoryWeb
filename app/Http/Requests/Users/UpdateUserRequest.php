<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    // Tentukan siapa saja yang boleh mengedit data pengguna ini (diatur true karena diurus oleh middleware)
    public function authorize(): bool
    {
        return true;
    }

    // Syarat saat menyimpan perubahan data (termasuk mengecualikan pengecekan duplikat email miliknya sendiri)
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z][a-zA-Z\s\.\'\-]*$/'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email,'.$this->route('user')->id],
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

    // Melindungi Superadmin agar tidak bisa mendowngrade peran atau menonaktifkan dirinya sendiri
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->route('user');
            $currentUser = auth()->user();

            if ($user && $currentUser && $user->id === $currentUser->id) {
                if ($this->input('role') !== $user->role->value) {
                    $validator->errors()->add('role', 'Anda tidak dapat mengubah peran (Role) akun Anda sendiri demi keamanan.');
                }
                if ($this->input('status') !== $user->status) {
                    $validator->errors()->add('status', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
                }
            }
        });
    }
}
