<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// Pabrik Data: Cetakan sakti untuk membuat puluhan/ratusan data dummy Barang Gudang secara acak.
class SparepartFactory extends Factory
{
    // Cetakan Dasar: Men-generate Part Number unik, harga acak (Rp 100rb - 5jt), stok, serta relasi Master Data.
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => $this->faker->word(),
            'part_number' => $this->faker->unique()->bothify('PART-####-????'),
            'brand_id' => \App\Models\Brand::factory(),
            'category_id' => \App\Models\Category::factory(),
            'location_id' => \App\Models\Location::factory(),
            'age' => $this->faker->randomElement(['Baru', 'Pernah Dipakai (Bekas)']), // Added age
            'condition' => $this->faker->randomElement(['Baik', 'Rusak', 'Hilang']), // Updated values
            'color' => $this->faker->safeColorName(), // Added color
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
