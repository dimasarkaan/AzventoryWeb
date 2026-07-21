<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// Pabrik Data: Mesin pembuat data dummy Akun Karyawan untuk keperluan testing atau seeding awal.
class UserFactory extends Factory
{
    // Enkripsi Password: Menyimpan password sementara agar tidak perlu di-hash berulang kali (Menghemat memori saat generate).
    protected static ?string $password;

    // Cetakan Dasar: Membuat profil user acak (Nama, Email unik, dan Role default: 'operator').
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'operator', // Default valid role
            'status' => 'aktif', // Required for RoleMiddleware
            'password_changed_at' => now(), // Bypass middleware
            'remember_token' => Str::random(10),
        ];
    }

    // Cetakan Khusus (State): Memalsukan akun yang alamat emailnya belum divalidasi (email_verified_at = null).
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
