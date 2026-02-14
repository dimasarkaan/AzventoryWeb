<?php

namespace Tests\Feature\SuperAdmin\Inventory;

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
        $this->get(route('superadmin.inventory.index'))->assertOk();
        $this->get(route('superadmin.inventory.show', $inventory))->assertOk();

        // Create
        $this->get(route('superadmin.inventory.create'))->assertOk();
        $this->post(route('superadmin.inventory.store'), Sparepart::factory()->raw())->assertRedirect();

        // Update
        $this->get(route('superadmin.inventory.edit', $inventory))->assertOk();
        $this->put(route('superadmin.inventory.update', $inventory), ['name' => 'Updated Name'])->assertRedirect();

        // Delete
        $this->delete(route('superadmin.inventory.destroy', $inventory))->assertRedirect();
        
        // Restore
        $inventory->delete();
        $this->patch(route('superadmin.inventory.restore', $inventory))->assertRedirect();

        // Force Delete
        $inventory->delete();
        $this->delete(route('superadmin.inventory.force-delete', $inventory))->assertRedirect();
    }

    /** @test */
    public function admin_can_access_most_actions_except_restore_and_force_delete()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $inventory = Sparepart::factory()->create();

        $this->actingAs($admin);

        // View - OK
        $this->get(route('superadmin.inventory.index'))->assertOk();
        
        // Create - OK
        $this->get(route('superadmin.inventory.create'))->assertOk();
        $this->post(route('superadmin.inventory.store'), Sparepart::factory()->raw())->assertRedirect();

        // Update - OK
        $this->get(route('superadmin.inventory.edit', $inventory))->assertOk();
        $this->put(route('superadmin.inventory.update', $inventory), ['name' => 'Updated Name'])->assertRedirect();

        // Delete - OK (Soft Delete)
        $this->delete(route('superadmin.inventory.destroy', $inventory))->assertRedirect();
        
        // Restore - FORBIDDEN
        $inventory->delete();
        $this->patch(route('superadmin.inventory.restore', $inventory))->assertForbidden();

        // Force Delete - FORBIDDEN
        $this->delete(route('superadmin.inventory.force-delete', $inventory))->assertForbidden();
    }

    /** @test */
    public function operator_can_only_view_create_and_update()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $inventory = Sparepart::factory()->create();

        $this->actingAs($operator);

        // View - OK
        $this->get(route('superadmin.inventory.index'))->assertOk();
        $this->get(route('superadmin.inventory.show', $inventory))->assertOk();

        // Create - OK
        $this->get(route('superadmin.inventory.create'))->assertOk();
        $this->post(route('superadmin.inventory.store'), Sparepart::factory()->raw())->assertRedirect();

        // Update - OK
        $this->get(route('superadmin.inventory.edit', $inventory))->assertOk();
        $this->put(route('superadmin.inventory.update', $inventory), ['name' => 'Updated Name'])->assertRedirect();

        // Delete - FORBIDDEN
        $this->delete(route('superadmin.inventory.destroy', $inventory))->assertForbidden();
        
        // Restore - FORBIDDEN
        $inventory->delete();
        $this->patch(route('superadmin.inventory.restore', $inventory))->assertForbidden();

        // Force Delete - FORBIDDEN
        $this->delete(route('superadmin.inventory.force-delete', $inventory))->assertForbidden();
    }
}
