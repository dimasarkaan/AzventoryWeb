<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InventoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user to perform actions
        $this->user = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(), // Bypass password change requirement
        ]);
    }

    public function test_inventory_list_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get(route('superadmin.inventory.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_new_sparepart()
    {
        $response = $this->actingAs($this->user)->post(route('superadmin.inventory.store'), [
            'name' => 'Keyboard Mechanical',
            'part_number' => 'KB-MECH-001',
            'brand' => 'Logitech', // Added
            'category' => 'Peripheral', 
            'location' => 'Rak A1',
            'condition' => 'Baru',
            'type' => 'asset', // Added
            'stock' => 10,
            'minimum_stock' => 2,
            'unit' => 'Unit',
            'price' => 500000,
            'status' => 'aktif',
        ]);

        if ($response->getSession()->get('errors')) {
            dump($response->getSession()->get('errors')->all());
        }

        $response->assertRedirect(route('superadmin.inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'name' => 'Keyboard Mechanical',
            'stock' => 10
        ]);
    }

    public function test_can_update_sparepart_stock()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->user)->put(route('superadmin.inventory.update', $sparepart), [
            'name' => $sparepart->name,
            'part_number' => $sparepart->part_number,
            'brand' => $sparepart->brand, // Added
            'category' => $sparepart->category,
            'location' => $sparepart->location,
            'condition' => $sparepart->condition,
            'type' => $sparepart->type, // Added
            'stock' => 50, // Updated stock
            'minimum_stock' => 5,
            'unit' => 'Unit',
            'price' => $sparepart->price,
            'status' => $sparepart->status,
        ]);

        $response->assertRedirect(route('superadmin.inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 50
        ]);
    }

    public function test_can_delete_sparepart()
    {
        $this->withoutExceptionHandling();
        $sparepart = Sparepart::factory()->create();
        
        $this->assertDatabaseCount('spareparts', 1); // Ensure it exists

        $response = $this->actingAs($this->user)->delete(route('superadmin.inventory.destroy', $sparepart));

        $response->assertRedirect(route('superadmin.inventory.index'));
        
        // Debug if it fails
        // if (\App\Models\Sparepart::count() > 0) {
        //      dump(\App\Models\Sparepart::all()->toArray());
        // }

        $this->assertDatabaseCount('spareparts', 0); // Should be 0
    }
}
