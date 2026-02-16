<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function superadmin_can_access_all_inventory_actions()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $inventory = Sparepart::factory()->create();

        $this->actingAs($superadmin);

        // View
        $this->get(route('inventory.index'))->assertOk();
        $this->get(route('inventory.show', $inventory))->assertOk();

        // Create
        $this->get(route('inventory.create'))->assertOk();
        $this->post(route('inventory.store'), Sparepart::factory()->raw())->assertRedirect();

        // Update
        $this->get(route('inventory.edit', $inventory))->assertOk();
        $this->put(route('inventory.update', $inventory), ['name' => 'Updated Name'])->assertRedirect();

        // Delete
        $this->delete(route('inventory.destroy', $inventory))->assertRedirect();
        
        // Restore
        $inventory->delete();
        $this->patch(route('inventory.restore', $inventory))->assertRedirect();

        // Force Delete
        $inventory->delete();
        $this->delete(route('inventory.force-delete', $inventory))->assertRedirect();
    }

    /** @test */
    public function admin_can_access_most_actions_except_restore_and_force_delete()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $inventory = Sparepart::factory()->create();

        $this->actingAs($admin);

        // View - OK
        $this->get(route('inventory.index'))->assertOk();
        
        // Create - OK
        $this->get(route('inventory.create'))->assertOk();
        $this->post(route('inventory.store'), Sparepart::factory()->raw())->assertRedirect();

        // Update - OK
        $this->get(route('inventory.edit', $inventory))->assertOk();
        $this->put(route('inventory.update', $inventory), ['name' => 'Updated Name'])->assertRedirect();

        // Delete - OK (Soft Delete)
        $this->delete(route('inventory.destroy', $inventory))->assertRedirect();
        
        // Restore - FORBIDDEN
        $inventory->delete();
        $this->patch(route('inventory.restore', $inventory))->assertForbidden();

        // Force Delete - FORBIDDEN
        $this->delete(route('inventory.force-delete', $inventory))->assertForbidden();
    }

    /** @test */
    public function operator_can_only_view()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $inventory = Sparepart::factory()->create();

        $this->actingAs($operator);

        // View - OK
        $this->get(route('inventory.index'))->assertOk();
        $this->get(route('inventory.show', $inventory))->assertOk();

        // Create - FORBIDDEN
        $this->get(route('inventory.create'))->assertForbidden();
        $this->post(route('inventory.store'), Sparepart::factory()->raw())->assertForbidden();

        // Update - FORBIDDEN
        $this->get(route('inventory.edit', $inventory))->assertForbidden();
        $this->put(route('inventory.update', $inventory), ['name' => 'Updated Name'])->assertForbidden();

        // Delete - FORBIDDEN
        $this->delete(route('inventory.destroy', $inventory))->assertForbidden();
        
        // Restore - FORBIDDEN
        $inventory->delete();
        $this->patch(route('inventory.restore', $inventory))->assertForbidden();

        // Force Delete - FORBIDDEN
        $this->delete(route('inventory.force-delete', $inventory))->assertForbidden();
    }
}
