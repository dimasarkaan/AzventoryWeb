<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Update 'good' -> 'Kondisi: Baik'
        DB::table('activity_logs')
            ->where('description', 'like', '%(good)%')
            ->update(['description' => DB::raw("REPLACE(description, '(good)', '(Kondisi: Baik)')")]);

        // Update 'bad' -> 'Kondisi: Rusak'
        DB::table('activity_logs')
            ->where('description', 'like', '%(bad)%')
            ->update(['description' => DB::raw("REPLACE(description, '(bad)', '(Kondisi: Rusak)')")]);

        // Update 'lost' -> 'Kondisi: Hilang'
        DB::table('activity_logs')
            ->where('description', 'like', '%(lost)%')
            ->update(['description' => DB::raw("REPLACE(description, '(lost)', '(Kondisi: Hilang)')")]);
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        // Kembalikan perubahan jika diperlukan
        DB::table('activity_logs')
            ->where('description', 'like', '%(Kondisi: Baik)%')
            ->update(['description' => DB::raw("REPLACE(description, '(Kondisi: Baik)', '(good)')")]);
            
        DB::table('activity_logs')
            ->where('description', 'like', '%(Kondisi: Rusak)%')
            ->update(['description' => DB::raw("REPLACE(description, '(Kondisi: Rusak)', '(bad)')")]);

        DB::table('activity_logs')
            ->where('description', 'like', '%(Kondisi: Hilang)%')
            ->update(['description' => DB::raw("REPLACE(description, '(Kondisi: Hilang)', '(lost)')")]);
    }
};
