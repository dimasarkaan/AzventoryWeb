<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model User sebagai representasi pengguna dalam sistem.
 * 
 * Mengatur autentikasi, otorisasi role, dan relasi ke data lain.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
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
    ];

    /**
     * Mendapatkan URL avatar pengguna.
     * 
     * Jika tidak ada avatar, akan menggunakan layanan UI Avatars.
     */
    protected function avatarUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->avatar 
                ? asset('storage/' . $this->avatar) 
                : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF',
        );
    }

    /**
     * Atribut yang harus disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Definisi casting tipe data atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
            'role' => \App\Enums\UserRole::class,
        ];
    }
    
    // Relasi ke data peminjaman.
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}
