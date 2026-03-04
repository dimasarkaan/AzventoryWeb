<?php

namespace Tests\Feature\Inventory;

use App\Models\Location;
use App\Models\Sparepart;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
    }

    #[Test]
    public function superadmin_can_list_locations()
    {
        Location::create(['name' => 'Tegal', 'is_default' => true]);
        Location::create(['name' => 'Cibubur', 'is_default' => true]);

        $response = $this->actingAs($this->superadmin)
            ->getJson(route('locations.index'));

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Tegal'])
            ->assertJsonFragment(['name' => 'Cibubur']);
    }

    #[Test]
    public function superadmin_can_store_location()
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson(route('locations.store'), [
                'name' => 'Semarang'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('locations', ['name' => 'Semarang']);
    }

    #[Test]
    public function superadmin_can_rename_location_and_update_items()
    {
        Location::create(['name' => 'Tegal']);
        Sparepart::factory()->create(['location' => 'Tegal', 'name' => 'Bolt 1']);
        Sparepart::factory()->create(['location' => 'Tegal', 'name' => 'Bolt 2']);

        $location = Location::where('name', 'Tegal')->first();

        $response = $this->actingAs($this->superadmin)
            ->putJson(route('locations.update', $location->id), [
                'name' => 'Tegal Baru'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('locations', ['name' => 'Tegal Baru']);
        $this->assertDatabaseMissing('locations', ['name' => 'Tegal']);

        $this->assertEquals(2, Sparepart::where('location', 'Tegal Baru')->count());
        $this->assertEquals(0, Sparepart::where('location', 'Tegal')->count());
    }

    #[Test]
    public function superadmin_cannot_delete_location_with_items()
    {
        Location::create(['name' => 'Tegal']);
        Sparepart::factory()->create(['location' => 'Tegal']);

        $location = Location::where('name', 'Tegal')->first();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('locations.destroy', $location->id));

        $response->assertStatus(422);
        
        $this->assertDatabaseHas('locations', ['name' => 'Tegal']);
    }

    #[Test]
    public function superadmin_can_delete_empty_location()
    {
        Location::create(['name' => 'Tegal']);

        $location = Location::where('name', 'Tegal')->first();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('locations.destroy', $location->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('locations', ['name' => 'Tegal']);
    }

    #[Test]
    public function operator_cannot_manage_locations()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        Location::create(['name' => 'Tegal']);
        $location = Location::where('name', 'Tegal')->first();

        $this->actingAs($operator)
            ->putJson(route('locations.update', $location->id), ['name' => 'Fail'])
            ->assertStatus(403);

        $this->actingAs($operator)
            ->deleteJson(route('locations.destroy', $location->id))
            ->assertStatus(403);
    }
}
