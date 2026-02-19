<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Sparepart;
use App\Models\Borrowing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
    }

    /** @test */
    public function system_prevents_negative_stock_input_on_create()
    {
        // Aksi: Coba buat sparepart dengan stok negatif
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.store'), [
            'name' => 'Negative Item',
            'part_number' => 'NEG-001',
            'category' => 'Test',
            'stock' => -10, // Invalid
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => 'Baru',
            'location' => 'Rack A',
            'status' => 'aktif'
        ]);

        // Assert
        $response->assertSessionHasErrors(['stock']);
        $this->assertDatabaseMissing('spareparts', ['part_number' => 'NEG-001']);
    }

    /** @test */
    public function system_prevents_negative_stock_through_borrowing()
    {
        // Persiapan: Item dengan 5 stok
        $item = Sparepart::factory()->create(['stock' => 5]);
        $user = User::factory()->create();

        // Aksi: Coba pinjam 10 item
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.borrow.store', $item->id), [
            'user_id' => $user->id,
            'quantity' => 10, // Exceeds stock
            'borrower_name' => $user->name,
            'borrowed_at' => now(),
            'notes' => 'Overdraft test'
        ]);

        // Assert
        $response->assertSessionHasErrors(); // Should fail validation or logic
        $this->assertEquals(5, $item->fresh()->stock); // Stock should remain unchanged
    }

    /** @test */
    public function cannot_delete_item_with_active_borrowing()
    {
        // Persiapan: Item dengan peminjaman aktif
        $item = Sparepart::factory()->create(['stock' => 10]);
        $user = User::factory()->create();
        
        Borrowing::create([
            'sparepart_id' => $item->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed'
        ]);

        // Aksi: Coba SOFT DELETE
        $response = $this->actingAs($this->superAdmin)->delete(route('inventory.destroy', $item->id));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Barang tidak dapat dihapus karena sedang dipinjam.');
        $this->assertDatabaseHas('spareparts', ['id' => $item->id]);
    }

    /** @test */
    public function merges_stock_if_duplicate_part_number_exists()
    {
        // Arrange
        $existing = Sparepart::factory()->create([
            'part_number' => 'EXISTING-001',
            'stock' => 10,
            // Pastikan field lain cocok untuk logika deteksi duplikat persis di Service
            'name' => 'Duplicate Item',
            'brand' => 'Test Brand',
            'category' => 'Test Cat',
            'location' => 'Rack B',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'sale',
            'unit' => 'Pcs',
            'price' => '10000.00',
            'color' => 'Hitam'
        ]);

        // Act
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.store'), [
            'name' => 'Duplicate Item',
            'part_number' => 'EXISTING-001', // PN Sama
            'brand' => 'Test Brand',
            'category' => 'Test Cat',
            'location' => 'Rack B',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'sale',
            'stock' => 5, // Tambah 5 lagi
            'minimum_stock' => 5,
            'status' => 'aktif',
            'unit' => 'Pcs',
            'price' => '10000.00',
            'color' => 'Hitam'
        ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success'); // Seharusnya sukses (digabungkan)
        
        $this->assertDatabaseCount('spareparts', 1); // Masih 1 item
        $this->assertEquals(15, $existing->fresh()->stock); // 10 + 5
    }
}
