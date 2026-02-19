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
        Schema::table('spareparts', function (Blueprint $table) {
            // Composite indexes untuk kombinasi filter yang sering digunakan
            $table->index(['category', 'status'], 'idx_spareparts_category_status');
            $table->index(['location', 'status'], 'idx_spareparts_location_status');
            $table->index(['type', 'status'], 'idx_spareparts_type_status');
            $table->index(['created_at', 'deleted_at'], 'idx_spareparts_created_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropIndex('idx_spareparts_category_status');
            $table->dropIndex('idx_spareparts_location_status');
            $table->dropIndex('idx_spareparts_type_status');
            $table->dropIndex('idx_spareparts_created_deleted');
        });
    }
};
