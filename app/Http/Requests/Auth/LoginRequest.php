<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    // Menentukan apakah form login ini terbuka untuk umum (true)
    public function authorize(): bool
    {
        return true;
    }

    // Syarat mutlak yang harus diisi pengguna (Kolom login dan password tidak boleh kosong)
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    // Terjemahan pesan peringatan jika pengguna lupa mengisi salah satu kolom
    public function messages(): array
    {
        return [
            'login.required' => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    // Proses inti: Mengecek kecocokan email/username dan password ke dalam database
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->input('login');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $login,
            'password' => $password,
        ];

        // Cari user berdasarkan email/username untuk cek status
        $user = \App\Models\User::where($fieldType, $login)->first();

        if ($user && $user->status !== 'aktif') {
            RateLimiter::hit($this->throttleKey());

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Login Ditolak',
                'description' => "Upaya login gagal karena akun sedang nonaktif (Status: {$user->status}).",
                'properties' => [
                    'ip' => $this->ip(),
                    'user_agent' => $this->header('User-Agent'),
                ],
            ]);

            throw ValidationException::withMessages([
                'login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.',
            ]);
        }

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            if ($user) {
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'Login Gagal',
                    'description' => "Upaya masuk gagal. Kata sandi yang dimasukkan salah.",
                    'properties' => [
                        'ip' => $this->ip(),
                        'user_agent' => $this->header('User-Agent'),
                    ],
                ]);
            }

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    // Sistem Keamanan: Mencegah serangan "Brute Force" (Spam percobaan login terus-menerus)
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    // Membuat kunci unik berdasarkan alamat IP pengguna untuk membatasi jumlah percobaan login mereka
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')).'|'.$this->ip());
    }
}
