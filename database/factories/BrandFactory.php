<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

// Pabrik Data Master: Memproduksi data dummy Merek secara otomatis.
class BrandFactory extends Factory
{
    // Cetakan Dasar: Membuat nama acak ditambah string unik (uniqid) agar terhindar dari error duplikasi nama.
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->word()).' '.uniqid(),
            'is_active' => true,
        ];
    }
}
