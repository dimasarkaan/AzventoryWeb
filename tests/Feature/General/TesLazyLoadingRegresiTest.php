<?php

namespace Tests\Feature\General;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tes regresi untuk memastikan tidak ada LazyLoadingViolationException
 * pada seluruh endpoint yang mengakses relasi Sparepart (category, brand, location).
 *
 * Bug asal: Migrasi mengubah kolom string → foreign key, tapi controller/service
 * belum menambahkan eager loading (.with()) sehingga strict mode Laravel melempar exception.
 */
class TesLazyLoadingRegresiTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup master data
        $category = Category::create(['name' => 'Elektronik', 'is_active' => true]);
        $brand = Brand::create(['name' => 'Samsung', 'is_active' => true]);
        $location = Location::create(['name' => 'Gudang A', 'is_active' => true]);

        $this->superadmin = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'role' => \App\Enums\UserRole::ADMIN,
            'password_changed_at' => now(),
        ]);

        $this->operator = User::factory()->create([
            'role' => \App\Enums\UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        $this->sparepart = Sparepart::create([
            'name' => 'Resistor 10K',
            'part_number' => 'PN-LLR-001',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'location_id' => $location->id,
            'stock' => 100,
            'minimum_stock' => 10,
            'condition' => 'Baik',
            'type' => 'asset',
            'unit' => 'Pcs',
            'age' => 'Baru',
            'status' => 'aktif',
        ]);
    }

    #[Test]
    public function halaman_detail_inventaris_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.show', $this->sparepart));

        $response->assertStatus(200);
        // Memastikan relasi tampil tanpa exception
        $response->assertSee('Elektronik');
        $response->assertSee('Samsung');
        $response->assertSee('Gudang A');
    }

    #[Test]
    public function halaman_edit_inventaris_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.edit', $this->sparepart));

        $response->assertStatus(200);
    }

    #[Test]
    public function endpoint_check_part_number_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.check-part-number', ['part_number' => 'PN-LLR-001']));

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
            'data' => [
                'brand' => 'Samsung',
                'category' => 'Elektronik',
            ],
        ]);
    }

    #[Test]
    public function global_search_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->getJson(route('global-search', ['query' => 'Resistor']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'menus',
            'spareparts',
            'users',
        ]);

        // Memastikan sparepart ditemukan dengan relasi location
        $spareparts = $response->json('spareparts');
        $this->assertNotEmpty($spareparts);
        $this->assertStringContainsString('Gudang A', $spareparts[0]['subtitle']);
    }

    #[Test]
    public function dashboard_superadmin_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('dashboard.superadmin'));

        $response->assertStatus(200);
    }

    #[Test]
    public function dashboard_admin_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.admin'));

        $response->assertStatus(200);
    }

    #[Test]
    public function dashboard_operator_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->operator)
            ->get(route('dashboard.operator'));

        $response->assertStatus(200);
    }

    #[Test]
    public function download_qr_label_tidak_lazy_loading_violation(): void
    {
        // Generate QR code first
        app(\App\Services\QrCodeService::class)->generate($this->sparepart);

        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.download', $this->sparepart));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    #[Test]
    public function halaman_daftar_inventaris_tidak_lazy_loading_violation(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.index'));

        $response->assertStatus(200);
        // Memastikan relasi tampil di tabel
        $response->assertSee('Resistor 10K');
    }

    #[Test]
    public function dashboard_superadmin_dengan_stok_menipis_tidak_lazy_loading(): void
    {
        // Buat item low stock agar widget stok menipis terisi
        $category2 = Category::firstOrCreate(['name' => 'Mekanik'], ['is_active' => true]);
        Sparepart::create([
            'name' => 'Bearing SKF',
            'part_number' => 'PN-LLR-002',
            'category_id' => $category2->id,
            'brand_id' => Brand::first()->id,
            'location_id' => Location::first()->id,
            'stock' => 2,
            'minimum_stock' => 10,
            'condition' => 'Baik',
            'type' => 'asset',
            'unit' => 'Pcs',
            'age' => 'Baru',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('dashboard.superadmin'));

        $response->assertStatus(200);
    }
}
