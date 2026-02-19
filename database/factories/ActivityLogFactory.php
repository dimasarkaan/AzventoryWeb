<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    // Definisikan state default model.
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
