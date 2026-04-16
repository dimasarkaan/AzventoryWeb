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
            $table->index('type', 'idx_spareparts_type');
            $table->index('condition', 'idx_spareparts_condition');
            $table->index('color', 'idx_spareparts_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropIndex('idx_spareparts_type');
            $table->dropIndex('idx_spareparts_condition');
            $table->dropIndex('idx_spareparts_color');
        });
    }
};
