<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Menambahkan kolom UUID ke tabel users dan spareparts.
     *
     * Model User dan Sparepart sudah menggunakan getRouteKeyName() = 'uuid'
     * dan auto-generate UUID di boot(), namun kolom ini belum pernah dibuat.
     *
     * Proses:
     * 1. Tambah kolom uuid (nullable dulu agar aman untuk data existing)
     * 2. Backfill UUID untuk semua record yang sudah ada
     * 3. Ubah kolom menjadi unique (tidak null karena sudah diisi semua)
     */
    public function up(): void
    {
        // === USERS ===
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Backfill UUID untuk semua user yang sudah ada (termasuk soft-deleted)
        DB::table('users')->whereNull('uuid')->orderBy('id')->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('uuid');
        });

        // === SPAREPARTS ===
        Schema::table('spareparts', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Backfill UUID untuk semua sparepart yang sudah ada (termasuk soft-deleted)
        DB::table('spareparts')->whereNull('uuid')->orderBy('id')->each(function ($item) {
            DB::table('spareparts')->where('id', $item->id)->update(['uuid' => (string) Str::uuid()]);
        });

        Schema::table('spareparts', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });

        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
