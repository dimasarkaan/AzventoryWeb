<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Cek jika kolom sudah ada untuk menghindari error duplikasi
        if (!Schema::hasColumn('spareparts', 'age')) {
            Schema::table('spareparts', function (Blueprint $table) {
                $table->string('age', 50)->default('Bekas')->after('condition'); 
            });

            // Isi data awal
            DB::statement("
                UPDATE spareparts 
                SET age = CASE 
                    WHEN `condition` = 'Baru' THEN 'Baru'
                    ELSE 'Bekas'
                END
            ");
        }
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        if (Schema::hasColumn('spareparts', 'age')) {
            Schema::table('spareparts', function (Blueprint $table) {
                $table->dropColumn('age');
            });
        }
    }
};
