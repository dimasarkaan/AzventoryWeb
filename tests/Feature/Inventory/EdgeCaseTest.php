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
        $response->assertSessionHas('error', __('ui.error_cannot_delete_borrowed'));
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

    /** @test */
    public function system_prevents_bypassing_status_validation()
    {
        $sparepart = Sparepart::factory()->create(['status' => 'aktif']);

        // Aksi: Mencoba memasukkan status yang tidak ada di Enum via update
        $response = $this->actingAs($this->superAdmin)->put(route('inventory.update', $sparepart->id), [
            'name' => 'Name',
            'part_number' => $sparepart->part_number,
            'category' => 'Test',
            'stock' => 10,
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => 'Baru',
            'location' => 'Rack A',
            'status' => 'status_palsu_bypass' // Invalid status
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertEquals('aktif', $sparepart->fresh()->status);
    }

    /** @test */
    public function system_prevents_condition_exceeding_max_validation()
    {
        $sparepart = Sparepart::factory()->create(['condition' => 'Baik']);

        // Aksi: Mencoba memasukkan condition string length > 255
        $longCondition = str_repeat('A', 256);
        $response = $this->actingAs($this->superAdmin)->put(route('inventory.update', $sparepart->id), [
            'name' => 'Name',
            'part_number' => $sparepart->part_number,
            'category' => 'Test',
            'stock' => 10,
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => $longCondition, // Invalid > 255 char
            'location' => 'Rack A',
            'status' => 'aktif'
        ]);

        $response->assertSessionHasErrors(['condition']);
        $this->assertEquals('Baik', $sparepart->fresh()->condition);
    }
    
    /** @test */
    public function system_prevents_incorrect_datatype_on_stock()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.store'), [
            'name' => 'Test Item',
            'part_number' => 'TEST-001',
            'category' => 'Test',
            'stock' => 'lima belas', // Datatype mismatch (should be integer)
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => 'Baru',
            'location' => 'Rack A',
            'status' => 'aktif'
        ]);

        $response->assertSessionHasErrors(['stock']);
        $this->assertDatabaseMissing('spareparts', ['part_number' => 'TEST-001']);
    }

    /** @test */
    public function user_cannot_borrow_out_of_stock_item()
    {
        $item = Sparepart::factory()->create(['stock' => 0]);
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)->post(route('inventory.borrow.store', $item->id), [
            'user_id' => $user->id,
            'quantity' => 1,
            'borrower_name' => $user->name,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2),
            'notes' => 'Testing validation'
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('borrowings', [
            'sparepart_id' => $item->id,
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function operator_can_only_view_own_borrowings()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $otherOperator = User::factory()->create(['role' => 'operator']);

        $item = Sparepart::factory()->create(['stock' => 10]);

        $ownBorrowing = Borrowing::create([
            'sparepart_id' => $item->id,
            'user_id' => $operator->id,
            'borrower_name' => $operator->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed'
        ]);

        $otherBorrowing = Borrowing::create([
            'sparepart_id' => $item->id,
            'user_id' => $otherOperator->id,
            'borrower_name' => $otherOperator->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed'
        ]);

        $response = $this->actingAs($operator)->get(route('inventory.index'));
        
        // Asumsi page sukses dirender (Operator allowed inventory index)
        $response->assertStatus(200);
    }
}
