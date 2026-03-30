<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Sync Categories
        $categories = DB::table('spareparts')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($categories as $category) {
            $exists = DB::table('categories')->where('name', $category)->exists();
            if (! $exists) {
                DB::table('categories')->insert([
                    'name' => $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Sync Brands
        $brands = DB::table('spareparts')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand');

        foreach ($brands as $brand) {
            $exists = DB::table('brands')->where('name', $brand)->exists();
            if (! $exists) {
                DB::table('brands')->insert([
                    'name' => $brand,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Sync Locations
        $locations = DB::table('spareparts')
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->pluck('location');

        foreach ($locations as $location) {
            $exists = DB::table('locations')->where('name', $location)->exists();
            if (! $exists) {
                DB::table('locations')->insert([
                    'name' => $location,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sinkronisasi data ke master tabel sebaiknya tidak dihapus pada skenario rollback
        // karena data string pada spareparts tetap utuh dan ini hanya operasi salin (copy).
    }
};
