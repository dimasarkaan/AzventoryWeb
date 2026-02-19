<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            // Indexes untuk activity timeline dan approval filtering
            $table->index(['user_id', 'created_at'], 'idx_stock_logs_user_created');
            $table->index(['status', 'created_at'], 'idx_stock_logs_status_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropIndex('idx_stock_logs_user_created');
            $table->dropIndex('idx_stock_logs_status_created');
        });
    }
};
