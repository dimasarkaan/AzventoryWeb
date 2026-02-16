<?php

namespace Tests\Feature\SuperAdmin;

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
        // Act: Attempt to create sparepart with negative stock
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
        // Arrange: Item with 5 stock
        $item = Sparepart::factory()->create(['stock' => 5]);
        $user = User::factory()->create();

        // Act: Attempt to borrow 10 items
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
        // Arrange: Item with active borrowing
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

        // Act: Attempt to SOFT DELETE
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
            // Ensure other fields match for exact duplicate detection logic in Service
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
            'part_number' => 'EXISTING-001', // Same PN
            'brand' => 'Test Brand',
            'category' => 'Test Cat',
            'location' => 'Rack B',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'sale',
            'stock' => 5, // Add 5 more
            'minimum_stock' => 5,
            'status' => 'aktif',
            'unit' => 'Pcs',
            'price' => '10000.00',
            'color' => 'Hitam'
        ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success'); // Should be success (merged)
        
        $this->assertDatabaseCount('spareparts', 1); // Still 1 item
        $this->assertEquals(15, $existing->fresh()->stock); // 10 + 5
    }
}
