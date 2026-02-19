<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(),
        ]);
    }

    // 1. Tes Logika Duplikat: Penggabungan Stok (Sukses)
    public function test_duplicate_item_merges_stock_when_input_stock_is_positive()
    {
        // Buat item awal dengan SEMUA field eksplisit agar cocok dengan data POST
        $existing = Sparepart::factory()->create([
            'part_number' => 'DUPLICATE-PN-123',
            'name' => 'Existing Item',
            'brand' => 'Existing Brand',
            'category' => 'Existing Category',
            'location' => 'Rak 1',
            'condition' => 'Baru',
            'age' => 'Baru', // Explicitly set
            'type' => 'sale',
            'stock' => 10,
            'price' => 100000,
            'unit' => 'Pcs',
            'color' => 'Black', // Explicitly set
        ]);

        // Post data duplikat persis
        $response = $this->actingAs($this->user)->post(route('inventory.store'), [
            'part_number' => 'DUPLICATE-PN-123',
            'name' => 'Existing Item',
            'brand' => 'Existing Brand',
            'category' => 'Existing Category',
            'location' => 'Rak 1',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'sale',
            'stock' => 5, // Adding 5
            'minimum_stock' => 1,
            'status' => 'aktif',
            'price' => 100000,
            'unit' => 'Pcs',
            'color' => 'Black',
        ]);

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');
        
        // Pastikan TIDAK ada item baru dibuat (Jumlah harus 1)
        $this->assertDatabaseCount('spareparts', 1);

        // Pastikan stok digabungkan (10 + 5 = 15)
        $this->assertDatabaseHas('spareparts', [
            'id' => $existing->id,
            'stock' => 15,
        ]);
    }

    // 2. Tes Logika Duplikat: Peringatan pada Stok 0
    public function test_duplicate_item_shows_warning_when_input_stock_is_zero()
    {
        // Buat item awal
        $existing = Sparepart::factory()->create([
            'part_number' => 'DUPLICATE-PN-123',
            'name' => 'Existing Item',
            'brand' => 'Existing Brand',
            'category' => 'Existing Category',
            'location' => 'Rak 1',
            'condition' => 'Baru',
            'age' => 'Baru', // Explicitly set
            'type' => 'sale',
            'stock' => 10,
            'price' => 100000,
            'unit' => 'Pcs',
            'color' => 'Black', // Explicitly set
        ]);

        // Post data duplikat persis dengan stok 0
        $response = $this->actingAs($this->user)->from(route('inventory.create'))
            ->post(route('inventory.store'), [
            'part_number' => 'DUPLICATE-PN-123',
            'name' => 'Existing Item',
            'brand' => 'Existing Brand',
            'category' => 'Existing Category',
            'location' => 'Rak 1',
            'condition' => 'Baru',
            'age' => 'Baru', // Added required field
            'type' => 'sale',
            'stock' => 0, // Adding 0
            'minimum_stock' => 1,
            'status' => 'aktif',
            'price' => 100000,
            'unit' => 'Pcs',
            'color' => 'Black', // Explicitly set
        ]);

        // Seharusnya TIDAK redirect ke index, tapi kembali ke create
        $response->assertRedirect(route('inventory.create'));
        
        // Pastikan stok TIDAK BERUBAH
        $this->assertDatabaseHas('spareparts', [
            'id' => $existing->id,
            'stock' => 10,
        ]);
        
        // Pastikan TIDAK ada item baru dibuat
        $this->assertDatabaseCount('spareparts', 1);
    }

    // 3. Tes Buat Item Baru (Cek Validasi Parsial)
    public function test_create_new_item_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('inventory.store'), [
            'part_number' => '', // Empty
            'name' => '', // Empty
            'stock' => -5, // Invalid
        ]);

        $response->assertSessionHasErrors(['part_number', 'name', 'stock']);
    }
}
