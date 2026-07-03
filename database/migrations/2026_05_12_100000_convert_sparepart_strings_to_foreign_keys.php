<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi kolom string (category, brand, location) di tabel spareparts
     * menjadi foreign key yang merujuk ke tabel master masing-masing.
     *
     * Proses:
     * 1. Sinkronisasi data string yang ada ke tabel master (agar tidak ada yang terlewat)
     * 2. Tambah kolom FK baru (nullable dulu)
     * 3. Isi kolom FK berdasarkan pencocokan nama string → id di tabel master
     * 4. Hapus kolom string lama
     */
    public function up(): void
    {
        // === LANGKAH 1: Pastikan semua string unik sudah ada di tabel master ===

        // Sync categories
        $existingCategories = DB::table('spareparts')
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category');

        foreach ($existingCategories as $name) {
            DB::table('categories')->insertOrIgnore([
                'name' => $name,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sync brands
        $existingBrands = DB::table('spareparts')
            ->select('brand')
            ->distinct()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->pluck('brand');

        foreach ($existingBrands as $name) {
            DB::table('brands')->insertOrIgnore([
                'name' => $name,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Sync locations
        $existingLocations = DB::table('spareparts')
            ->select('location')
            ->distinct()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->pluck('location');

        foreach ($existingLocations as $name) {
            DB::table('locations')->insertOrIgnore([
                'name' => $name,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // === LANGKAH 2: Tambah kolom FK baru (nullable dulu agar aman) ===

        Schema::table('spareparts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('restrict');
            $table->foreignId('brand_id')->nullable()->after('brand')->constrained('brands')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->after('location')->constrained('locations')->onDelete('restrict');
        });

        // === LANGKAH 3: Isi kolom FK berdasarkan string lama ===

        // Map category string → category_id
        $categories = DB::table('categories')->pluck('id', 'name');
        foreach ($categories as $name => $id) {
            DB::table('spareparts')
                ->where('category', $name)
                ->update(['category_id' => $id]);
        }

        // Map brand string → brand_id
        $brands = DB::table('brands')->pluck('id', 'name');
        foreach ($brands as $name => $id) {
            DB::table('spareparts')
                ->where('brand', $name)
                ->update(['brand_id' => $id]);
        }

        // Map location string → location_id
        $locations = DB::table('locations')->pluck('id', 'name');
        foreach ($locations as $name => $id) {
            DB::table('spareparts')
                ->where('location', $name)
                ->update(['location_id' => $id]);
        }

        // === LANGKAH 4: Hapus kolom string lama dan buat FK not null ===

        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropIndex('idx_spareparts_category_status');
            $table->dropIndex('idx_spareparts_location_status');
            $table->dropIndex(['category']);
            $table->dropIndex(['brand']);
            $table->dropIndex(['location']);
            $table->dropColumn(['category', 'brand', 'location']);

            // Re-create composite index with new ID columns
            $table->index(['category_id', 'status'], 'idx_spareparts_category_status');
            $table->index(['location_id', 'status'], 'idx_spareparts_location_status');
        });
    }

    /**
     * Kembalikan migrasi: buat ulang kolom string dan isi dari tabel master.
     */
    public function down(): void
    {
        // Tambah kembali kolom string
        Schema::table('spareparts', function (Blueprint $table) {
            $table->string('category')->nullable()->after('part_number')->index();
            $table->string('brand')->nullable()->after('category')->index();
            $table->string('location')->nullable()->after('brand')->index();
        });

        // Isi kolom string dari tabel master
        $spareparts = DB::table('spareparts')->get();
        foreach ($spareparts as $sp) {
            $catName = $sp->category_id ? DB::table('categories')->where('id', $sp->category_id)->value('name') : null;
            $brandName = $sp->brand_id ? DB::table('brands')->where('id', $sp->brand_id)->value('name') : null;
            $locName = $sp->location_id ? DB::table('locations')->where('id', $sp->location_id)->value('name') : null;

            DB::table('spareparts')->where('id', $sp->id)->update([
                'category' => $catName,
                'brand' => $brandName,
                'location' => $locName,
            ]);
        }

        // Hapus kolom FK
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['location_id']);
            $table->dropColumn(['category_id', 'brand_id', 'location_id']);
        });
    }
};
