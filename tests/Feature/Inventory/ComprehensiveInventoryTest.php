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

    // 1. Test Duplicate Logic: Merging Stock (Success)
    public function test_duplicate_item_merges_stock_when_input_stock_is_positive()
    {
        // Create initial item with ALL fields explicit to match POST data
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

        // Post exact duplicate data
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
        
        // Assert NO new item created (Count should be 1)
        $this->assertDatabaseCount('spareparts', 1);

        // Assert stock merged (10 + 5 = 15)
        $this->assertDatabaseHas('spareparts', [
            'id' => $existing->id,
            'stock' => 15,
        ]);
    }

    // 2. Test Duplicate Logic: Warning on 0 Stock
    public function test_duplicate_item_shows_warning_when_input_stock_is_zero()
    {
        // Create initial item
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

        // Post exact duplicate data with 0 stock
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

        // Should NOT redirect to index, but back to create
        $response->assertRedirect(route('inventory.create'));
        
        // Assert stock UNCHANGED
        $this->assertDatabaseHas('spareparts', [
            'id' => $existing->id,
            'stock' => 10,
        ]);
        
        // Assert NO new item created
        $this->assertDatabaseCount('spareparts', 1);
    }

    // 3. Test Create New Item (Partial Validation Check)
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
