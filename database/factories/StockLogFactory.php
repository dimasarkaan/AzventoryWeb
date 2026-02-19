<?php

namespace Database\Factories;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockLog>
 */
class StockLogFactory extends Factory
{
    // Definisikan state default model.
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
