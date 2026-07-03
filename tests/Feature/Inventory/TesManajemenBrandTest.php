<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesManajemenBrandTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
    }

    #[Test]
    public function superadmin_dapat_melihat_daftar_brand()
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
    public function superadmin_dapat_menyimpan_brand()
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson(route('brands.store'), [
                'name' => 'Asus',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('brands', ['name' => 'Asus']);
    }

    #[Test]
    public function superadmin_dapat_mengubah_nama_brand_dan_update_item()
    {
        $brand = Brand::create(['name' => 'Logitec']); // Typo
        Sparepart::factory()->create(['brand_id' => $brand->id, 'name' => 'Mouse M170']);
        Sparepart::factory()->create(['brand_id' => $brand->id, 'name' => 'Keyboard K580']);

        $response = $this->actingAs($this->superadmin)
            ->patchJson(route('brands.update', $brand->id), [
                'name' => 'Logitech', // Corrected
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('brands', ['name' => 'Logitech']);
        $this->assertDatabaseMissing('brands', ['name' => 'Logitec']);

        $this->assertEquals(2, Sparepart::where('brand_id', $brand->id)->count());
    }

    #[Test]
    public function superadmin_tidak_dapat_menghapus_brand_yang_memiliki_item()
    {
        $brand = Brand::create(['name' => 'Lenovo']);
        Sparepart::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('brands.destroy', $brand->id));

        $response->assertStatus(422);

        $this->assertDatabaseHas('brands', ['name' => 'Lenovo']);
    }

    #[Test]
    public function superadmin_dapat_menghapus_brand_kosong()
    {
        Brand::create(['name' => 'HP']);

        $brand = Brand::where('name', 'HP')->first();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('brands.destroy', $brand->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('brands', ['name' => 'HP']);
    }

    #[Test]
    public function operator_tidak_dapat_mengelola_brand()
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

    #[Test]
    public function admin_dapat_menyimpan_brand_baru_secara_dinamis()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)
            ->postJson(route('brands.store'), [
                'name' => 'Brand Dinamis Admin',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('brands', ['name' => 'Brand Dinamis Admin']);
    }

    #[Test]
    public function admin_tidak_dapat_mengubah_atau_menghapus_brand()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $brand = Brand::create(['name' => 'Brand Statis']);

        $this->actingAs($admin)
            ->patchJson(route('brands.update', $brand->id), ['name' => 'Changed'])
            ->assertStatus(403);

        $this->actingAs($admin)
            ->deleteJson(route('brands.destroy', $brand->id))
            ->assertStatus(403);
    }
}
