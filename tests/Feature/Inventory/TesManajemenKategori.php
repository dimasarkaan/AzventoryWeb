<?php

namespace Tests\Feature\Inventory;

use App\Models\Category;
use App\Models\Sparepart;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesManajemenKategori extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
    }

    #[Test]
    public function superadmin_dapat_melihat_daftar_kategori()
    {
        Category::create(['name' => 'IC']);
        Category::create(['name' => 'Kapasitor']);

        $response = $this->actingAs($this->superadmin)
            ->getJson(route('categories.index'));

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'IC'])
            ->assertJsonFragment(['name' => 'Kapasitor']);
    }

    #[Test]
    public function superadmin_dapat_menyimpan_kategori()
    {
        $response = $this->actingAs($this->superadmin)
            ->postJson(route('categories.store'), [
                'name' => 'Resistor'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'Resistor']);
    }

    #[Test]
    public function superadmin_dapat_mengubah_nama_kategori_dan_update_item()
    {
        Category::create(['name' => 'Layar']);
        Sparepart::factory()->create(['category' => 'Layar', 'name' => 'LCD Samsung']);
        Sparepart::factory()->create(['category' => 'Layar', 'name' => 'LCD iPhone']);

        $category = Category::where('name', 'Layar')->first();

        $response = $this->actingAs($this->superadmin)
            ->patchJson(route('categories.update', $category->id), [
                'name' => 'Screen'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['name' => 'Screen']);
        $this->assertDatabaseMissing('categories', ['name' => 'Layar']);

        $this->assertEquals(2, Sparepart::where('category', 'Screen')->count());
        $this->assertEquals(0, Sparepart::where('category', 'Layar')->count());
    }

    #[Test]
    public function superadmin_tidak_dapat_menghapus_kategori_yang_memiliki_item()
    {
        Category::create(['name' => 'IC']);
        Sparepart::factory()->create(['category' => 'IC']);

        $category = Category::where('name', 'IC')->first();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('categories.destroy', $category->id));

        $response->assertStatus(422);
        
        $this->assertDatabaseHas('categories', ['name' => 'IC']);
    }

    #[Test]
    public function superadmin_dapat_menghapus_kategori_kosong()
    {
        Category::create(['name' => 'Memory']);

        $category = Category::where('name', 'Memory')->first();

        $response = $this->actingAs($this->superadmin)
            ->deleteJson(route('categories.destroy', $category->id));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['name' => 'Memory']);
    }

    #[Test]
    public function operator_tidak_dapat_mengelola_kategori()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        Category::create(['name' => 'IC']);
        $category = Category::where('name', 'IC')->first();

        $this->actingAs($operator)
            ->patchJson(route('categories.update', $category->id), ['name' => 'Fail'])
            ->assertStatus(403);

        $this->actingAs($operator)
            ->deleteJson(route('categories.destroy', $category->id))
            ->assertStatus(403);
            
        $this->actingAs($operator)
            ->postJson(route('categories.store'), ['name' => 'Fail'])
            ->assertStatus(403);
    }
}


