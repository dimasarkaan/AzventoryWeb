<?php

namespace Database\Factories;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

// Pabrik Data: Cetakan otomatis untuk merekayasa data riwayat keluar-masuk barang di gudang.
class StockLogFactory extends Factory
{
    // Cetakan Dasar: Memilih acak tipe mutasi ('masuk'/'keluar') dengan status 'approved'.
    public function definition(): array
    {
        return [
            'sparepart_id' => Sparepart::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['masuk', 'keluar']),
            'quantity' => $this->faker->numberBetween(1, 10),
            'reason' => $this->faker->sentence(), // Replaced notes with reason
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
