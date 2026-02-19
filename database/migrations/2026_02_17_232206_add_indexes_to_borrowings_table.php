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
        Schema::table('borrowings', function (Blueprint $table) {
            // Indexes untuk query filtering dan join optimization
            $table->index('status', 'idx_borrowings_status');
            $table->index(['sparepart_id', 'status'], 'idx_borrowings_sparepart_status');
            $table->index(['user_id', 'returned_at'], 'idx_borrowings_user_returned');
            $table->index('expected_return_at', 'idx_borrowings_expected_return');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropIndex('idx_borrowings_status');
            $table->dropIndex('idx_borrowings_sparepart_status');
            $table->dropIndex('idx_borrowings_user_returned');
            $table->dropIndex('idx_borrowings_expected_return');
        });
    }
};
