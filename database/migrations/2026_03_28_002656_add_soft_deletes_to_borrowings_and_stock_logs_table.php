<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom deleted_at untuk mendukung Soft Delete pada tabel borrowings dan stock_logs.
     * Ini memastikan riwayat transaksi (audit trail) tidak pernah hilang secara permanen
     * meskipun data asal (Sparepart) sudah dihapus.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('stock_logs', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
