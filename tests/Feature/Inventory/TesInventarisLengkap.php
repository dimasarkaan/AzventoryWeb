<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesInventarisLengkap extends TestCase
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
    public function test_item_duplikat_menggabungkan_stok_ketika_input_stok_positif()
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
    public function test_item_duplikat_menampilkan_peringatan_ketika_input_stok_adalah_nol()
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
    public function test_buat_item_baru_memvalidasi_field_yang_wajib_diisi()
    {
        $response = $this->actingAs($this->user)->post(route('inventory.store'), [
            'part_number' => '', // Empty
            'name' => '', // Empty
            'stock' => -5, // Invalid
        ]);

        $response->assertSessionHasErrors(['part_number', 'name', 'stock']);
    }
}

