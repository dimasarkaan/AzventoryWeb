<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk optimasi performa tabel notifikasi.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Index komposit untuk mempercepat penghitungan unread notifications (COUNT & Filter)
            // Urutan: notifiable_id & type dulu (karena ini morphs), lalu read_at
            $table->index(['notifiable_id', 'notifiable_type', 'read_at'], 'notifications_unread_index');

            // Index komposit untuk mempercepat pengurutan listing terbaru (Pagination)
            $table->index(['notifiable_id', 'notifiable_type', 'created_at'], 'notifications_latest_index');
        });
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_unread_index');
            $table->dropIndex('notifications_latest_index');
        });
    }
};
