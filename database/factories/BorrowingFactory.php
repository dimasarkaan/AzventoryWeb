<?php

namespace Database\Factories;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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
