<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom rejection_reason ke tabel stock_logs untuk menyimpan alasan penolakan.
     */
    public function up(): void
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->string('rejection_reason', 500)->nullable()->after('approved_by');
        });
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
