<?php

namespace Tests\Feature\Api;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiDataMasterTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role, 'password_changed_at' => now()]);
    }

    #[Test]
    public function crud_brands_berhasil_via_api()
    {
        Sanctum::actingAs($this->makeUser());

        // Create
        $this->postJson('/api/v1/brands', ['name' => 'BRAND-API'])
            ->assertStatus(201);
        $this->assertDatabaseHas('brands', ['name' => 'BRAND-API']);

        $brand = Brand::first();

        // Update
        $this->putJson("/api/v1/brands/{$brand->id}", ['name' => 'BRAND-API-UPDATED'])
            ->assertStatus(200);
        $this->assertDatabaseHas('brands', ['name' => 'BRAND-API-UPDATED']);

        // List
        $this->getJson('/api/v1/brands')->assertStatus(200);

        // Delete
        $this->deleteJson("/api/v1/brands/{$brand->id}")->assertStatus(200);
        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    #[Test]
    public function crud_categories_berhasil_via_api()
    {
        Sanctum::actingAs($this->makeUser());

        $this->postJson('/api/v1/categories', ['name' => 'CAT-API'])->assertStatus(201);
        $cat = Category::first();
        $this->putJson("/api/v1/categories/{$cat->id}", ['name' => 'CAT-API-NEW'])->assertStatus(200);
        $this->deleteJson("/api/v1/categories/{$cat->id}")->assertStatus(200);
    }

    #[Test]
    public function crud_locations_berhasil_via_api()
    {
        Sanctum::actingAs($this->makeUser());

        $this->postJson('/api/v1/locations', ['name' => 'LOC-API'])->assertStatus(201);
        $loc = Location::first();
        $this->putJson("/api/v1/locations/{$loc->id}", ['name' => 'LOC-API-NEW'])->assertStatus(200);
        $this->deleteJson("/api/v1/locations/{$loc->id}")->assertStatus(200);
    }
}
