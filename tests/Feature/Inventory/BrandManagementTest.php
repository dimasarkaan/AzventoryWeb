<?php

namespace Tests\Feature\Inventory;

use App\Models\Brand;
use App\Models\Sparepart;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrandManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
    }

    #[Test]
    public function superadmin_can_list_brands()
    {
        Brand::create(['name' => 'Dell']);
        Brand::create(['name' => 'Logitech']);

        $response = $this->actingAs($this->superadmin)
            ->getJson(route('brands.index'));

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Dell'])
            ->assertJsonFragment(['name' => 'Logitech']);
    }

    #[Test]
    public function superadmin_can_store_brand()
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson(route('brands.store'), [
                'name' => 'Asus'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('brands', ['name' => 'Asus']);
    }

    #[Test]
    public function superadmin_can_rename_brand_and_update_items()
    {
        Brand::create(['name' => 'Logitec']); // Typo
        Sparepart::factory()->create(['brand' => 'Logitec', 'name' => 'Mouse M170']);
        Sparepart::factory()->create(['brand' => 'Logitec', 'name' => 'Keyboard K580']);

        $brand = Brand::where('name', 'Logitec')->first();

        $response = $this->actingAs($this->superadmin)
            ->patchJson(route('brands.update', $brand->id), [
                'name' => 'Logitech' // Corrected
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('brands', ['name' => 'Logitech']);
        $this->assertDatabaseMissing('brands', ['name' => 'Logitec']);

        $this->assertEquals(2, Sparepart::where('brand', 'Logitech')->count());
        $this->assertEquals(0, Sparepart::where('brand', 'Logitec')->count());
    }

    #[Test]
    public function superadmin_cannot_delete_brand_with_items()
    {
        Brand::create(['name' => 'Lenovo']);
        Sparepart::factory()->create(['brand' => 'Lenovo']);

        $brand = Brand::where('name', 'Lenovo')->first();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('brands.destroy', $brand->id));

        $response->assertStatus(422);
        
        $this->assertDatabaseHas('brands', ['name' => 'Lenovo']);
    }

    #[Test]
    public function superadmin_can_delete_empty_brand()
    {
        Brand::create(['name' => 'HP']);

        $brand = Brand::where('name', 'HP')->first();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('brands.destroy', $brand->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('brands', ['name' => 'HP']);
    }

    #[Test]
    public function operator_cannot_manage_brands()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        Brand::create(['name' => 'Dell']);
        $brand = Brand::where('name', 'Dell')->first();

        $this->actingAs($operator)
            ->patchJson(route('brands.update', $brand->id), ['name' => 'Fail'])
            ->assertStatus(403);

        $this->actingAs($operator)
            ->deleteJson(route('brands.destroy', $brand->id))
            ->assertStatus(403);
            
        $this->actingAs($operator)
            ->postJson(route('brands.store'), ['name' => 'Fail'])
            ->assertStatus(403);
    }
}
