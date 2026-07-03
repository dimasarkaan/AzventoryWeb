<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Lokasi '.$this->faker->word().' '.uniqid(),
            'is_active' => true,
        ];
    }
}
