<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Menjalankan database seeders.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@azventory.com',
            'role' => 'superadmin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@azventory.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Operator',
            'username' => 'operator',
            'email' => 'operator@azventory.com',
            'role' => 'operator',
            'password' => bcrypt('password'),
        ]);
    }
}
