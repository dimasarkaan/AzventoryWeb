<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sparepart>
 */
class SparepartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'part_number' => $this->faker->unique()->bothify('PART-####-????'),
            'brand' => $this->faker->company(), // Added brand
            'category' => $this->faker->randomElement(['Processor', 'RAM', 'Motherboard', 'Storage', 'PSU']),
            'location' => $this->faker->randomElement(['Gudang A', 'Rak B', 'Etalase Depan']),
            'condition' => $this->faker->randomElement(['Baru', 'Bekas', 'Rusak']),
            'type' => $this->faker->randomElement(['sale', 'asset']), // Added type
            'price' => $this->faker->numberBetween(100000, 5000000),
            'stock' => $this->faker->numberBetween(0, 100),
            'minimum_stock' => 5,
            'unit' => 'Unit',
             // Add default values for other fields if needed, like status
            'status' => 'aktif',
        ];
    }
}
