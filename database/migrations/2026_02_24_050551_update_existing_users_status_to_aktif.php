<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('users')
            ->where('status', 'active')
            ->update(['status' => 'aktif']);

        \Illuminate\Support\Facades\DB::table('users')
            ->where('status', 'inactive')
            ->update(['status' => 'nonaktif']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('users')
            ->where('status', 'aktif')
            ->update(['status' => 'active']);

        \Illuminate\Support\Facades\DB::table('users')
            ->where('status', 'nonaktif')
            ->update(['status' => 'inactive']);
    }
};
