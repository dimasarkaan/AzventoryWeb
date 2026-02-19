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
        Schema::table('spareparts', function (Blueprint $table) {
            $table->string('age', 50)->change();
        });
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->string('age', 10)->change();
        });
    }
};
