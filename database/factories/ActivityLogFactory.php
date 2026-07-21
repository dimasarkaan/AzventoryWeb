<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

// Pabrik Data: Cetakan otomatis untuk membuat data dummy Log Aktivitas (Jejak Digital) saat testing/seeding.
class ActivityLogFactory extends Factory
{
    // Cetakan Dasar: Membuat rekayasa alamat IP, aksi, dan deskripsi sistem secara acak (Faker).
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'properties' => ['ip' => $this->faker->ipv4()],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
