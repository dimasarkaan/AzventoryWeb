<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->word()).' '.uniqid(),
            'is_active' => true,
        ];
    }
}
