<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists to avoid duplication errors
        if (!Schema::hasColumn('spareparts', 'age')) {
            Schema::table('spareparts', function (Blueprint $table) {
                $table->string('age', 50)->default('Bekas')->after('condition'); 
            });

            // Populate initial data
            DB::statement("
                UPDATE spareparts 
                SET age = CASE 
                    WHEN condition = 'Baru' THEN 'Baru'
                    ELSE 'Bekas'
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('spareparts', 'age')) {
            Schema::table('spareparts', function (Blueprint $table) {
                $table->dropColumn('age');
            });
        }
    }
};
