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
        // Create initial item
        $existing = Sparepart::factory()->create([
            'part_number' => 'DUPLICATE-PN-123',
            'name' => 'Existing Item',
            'brand' => 'Existing Brand',
            'category' => 'Existing Category',
            'location' => 'Rak 1',
            'condition' => 'Baru',
            'type' => 'sale',
            'stock' => 10,
            'price' => 100000,
            'unit' => 'Pcs',
            'color' => null,
        ]);

        // Post exact duplicate data but with new stock
        $response = $this->actingAs($this->user)->post(route('superadmin.inventory.store'), [
            'part_number' => 'DUPLICATE-PN-123',
            'name' => 'Existing Item',
            'brand' => 'Existing Brand',
            'category' => 'Existing Category',
            'location' => 'Rak 1',
            'condition' => 'Baru',
            'type' => 'sale',
            'stock' => 5, // Adding 5
            'minimum_stock' => 1,
            'status' => 'aktif',
            'price' => 100000,
            'unit' => 'Pcs',
        ]);

        $response->assertRedirect(route('superadmin.inventory.index'));
        $response->assertSessionHas('success');
        
        // Assert stock merged (10 + 5 = 15)
        $this->assertDatabaseHas('spareparts', [
            'id' => $existing->id,
            'stock' => 15,
        ]);
        
        // Assert NO new item created
        $this->assertDatabaseCount('spareparts', 1);
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
            'type' => 'sale',
            'stock' => 10,
            'price' => 100000,
            'unit' => 'Pcs',
            'color' => null,
        ]);

        // Post exact duplicate data with 0 stock
        $response = $this->actingAs($this->user)->from(route('superadmin.inventory.create'))
            ->post(route('superadmin.inventory.store'), [
            'part_number' => 'DUPLICATE-PN-123',
            'name' => 'Existing Item',
            'brand' => 'Existing Brand',
            'category' => 'Existing Category',
            'location' => 'Rak 1',
            'condition' => 'Baru',
            'type' => 'sale',
            'stock' => 0, // Adding 0
            'minimum_stock' => 1,
            'status' => 'aktif',
            'price' => 100000,
            'unit' => 'Pcs',
        ]);

        // dump('Session keys:', array_keys($response->getSession()->all()));
        // if ($response->getSession()->get('errors')) {
        //     dump('Errors:', $response->getSession()->get('errors')->all());
        // }
        // dump('Redirect Location:', $response->headers->get('Location'));

        // Should NOT redirect to index, but back to create
        $response->assertRedirect(route('superadmin.inventory.create'));
        
        // Should have WARNING session flash
        $response->assertSessionHas('warning');
        
        // Assert stock UNCHANGED
        $this->assertDatabaseHas('spareparts', [
            'id' => $existing->id,
            'stock' => 10,
        ]);
    }

    // 3. Test Create New Item (Partial Validation Check)
    public function test_create_new_item_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('superadmin.inventory.store'), [
            'part_number' => '', // Empty
            'name' => '', // Empty
            'stock' => -5, // Invalid
        ]);

        $response->assertSessionHasErrors(['part_number', 'name', 'stock']);
    }
}
