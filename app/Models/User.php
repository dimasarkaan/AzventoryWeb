<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Model (Blueprint Tabel Database) Induk untuk mengurus akun Pengguna (User).
// Berperan penuh mengatur Login (Autentikasi), Hak Akses (Otorisasi), dan Data Profil.
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    // Atribut (Kolom) yang diizinkan untuk diisi data secara bebas ke dalam database
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'jabatan',
        'status',
        'avatar',
        'phone',
        'address',
        'password_changed_at',
        'is_username_changed',
        'settings',
    ];

    // Fitur Cerdas: Menyediakan foto profil bawaan (Inisial Nama) jika pengguna belum mengunggah foto sendiri
    protected function avatarUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->avatar
                ? asset('storage/'.$this->avatar)
                : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF',
        );
    }

    // Atribut Rahasia: Kolom yang diharamkan ikut terbawa/tampil saat data user dipanggil (Demi Keamanan)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Konversi Tipe Data: Memaksa format data agar selalu benar (Contoh: teks diubah paksa jadi format Tanggal/Waktu)
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
            'role' => \App\Enums\UserRole::class,
            'settings' => 'array',
        ];
    }

    // Relasi ke data peminjaman.
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // Keamanan URL: Menggunakan gabungan huruf acak (UUID) di URL Profil alih-alih angka berurutan (agar tidak mudah ditebak)
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Kompatibilitas URL: Menjaga agar link URL versi lama (yang masih pakai angka ID biasa) tetap bisa diakses dan tidak error
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'uuid' && is_numeric($value)) {
            return $this->where('id', $value)->first();
        }

        return $this->where($field, $value)->first();
    }

    // Mendaftarkan kolom 'uuid' agar otomatis diisi gabungan angka/huruf acak oleh sistem saat user baru diciptakan
    public function uniqueIds()
    {
        return ['uuid'];
    }
}
