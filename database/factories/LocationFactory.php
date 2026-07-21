<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

// Pabrik Data Master: Memproduksi data dummy Lokasi Gudang secara otomatis.
class LocationFactory extends Factory
{
    // Cetakan Dasar: Membuat nama acak ditambah string unik (uniqid) agar terhindar dari error duplikasi nama.
    public function definition(): array
    {
        return [
            'name' => 'Lokasi '.$this->faker->word().' '.uniqid(),
            'is_active' => true,
        ];
    }
}
