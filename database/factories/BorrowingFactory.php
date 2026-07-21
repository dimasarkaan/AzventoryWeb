<?php

namespace Database\Factories;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

// Pabrik Data: Cetakan otomatis untuk memproduksi data dummy Transaksi Peminjaman secara instan.
class BorrowingFactory extends Factory
{
    // Cetakan Dasar: Menyimulasikan peminjam, mengatur status 'borrowed', dan menyetel tenggat waktu 7 hari ke depan.
    public function definition(): array
    {
        return [
            'sparepart_id' => Sparepart::factory(),
            'user_id' => User::factory(),
            'borrower_name' => $this->faker->name(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
            'notes' => $this->faker->sentence(),
        ];
    }
}
