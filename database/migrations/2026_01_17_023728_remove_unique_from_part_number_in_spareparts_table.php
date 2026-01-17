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
            $table->dropUnique(['part_number']);
            $table->index('part_number');
        });
    }

    public function down(): void
    {
        // Schema::table('spareparts', function (Blueprint $table) {
        //     $table->dropIndex(['part_number']);
        //     $table->unique('part_number');
        // });
    }
};
