<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // 1. Barang/Sparepart
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('part_number')->index();
            $table->string('category')->index();
            $table->string('brand')->nullable()->index();
            $table->string('location')->index();
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->integer('stock');
            $table->string('unit')->default('Pcs');
            $table->enum('type', ['sale', 'asset'])->default('sale');
            $table->string('condition'); // Baru, Bekas, dll
            $table->decimal('price', 15, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('color')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Peminjaman (Bergantung pada Sparepart dan User)
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('borrower_name'); // Nama peminjam manual (jika bukan user)
            $table->integer('quantity');
            $table->timestamp('borrowed_at');
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'lost'])->default('borrowed');
            
            // Return Check
            $table->string('return_condition')->nullable();
            $table->text('return_notes')->nullable();
            $table->json('return_photos')->nullable();
            
            $table->timestamps();
        });

        // 3. Log Stok (Bergantung pada Sparepart dan User)
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User pengaju
            $table->enum('type', ['masuk', 'keluar']);
            $table->unsignedInteger('quantity');
            $table->string('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Approval
            $table->timestamps();
        });
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
        Schema::dropIfExists('borrowings');
        Schema::dropIfExists('spareparts');
    }
};
