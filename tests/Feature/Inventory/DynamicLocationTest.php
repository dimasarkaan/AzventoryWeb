<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DynamicLocationTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected $admin;

    protected $operator;

    protected $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->inventoryService = app(InventoryService::class);

        \App\Models\Location::create(['name' => 'Tegal']);
        \App\Models\Location::create(['name' => 'Cibubur']);

        Cache::forget('inventory_locations');
    }

    #[Test]
    public function lokasi_default_selalu_ada()
    {
        $options = $this->inventoryService->getDropdownOptions();
        $locations = $options['locations']->toArray();

        $this->assertContains('Tegal', $locations);
        $this->assertContains('Cibubur', $locations);
    }

    #[Test]
    public function lokasi_baru_tersimpan_dan_digabungkan()
    {
        $this->actingAs($this->superAdmin);

        // Create an item with a new location
        $newLocation = 'Gudang Cileungsi';
        $itemData = [
            'name' => 'Barang Baru',
            'part_number' => 'PN-NEW-001',
            'brand' => 'Merk A',
            'category' => 'Kategori A',
            'location' => $newLocation,
            'age' => 'Baru',
            'condition' => 'Baik',
            'type' => 'asset',
            'stock' => 10,
            'status' => 'aktif',
        ];

        $response = $this->post(route('inventory.store'), $itemData);
        $response->assertStatus(302); // Redirect after successful creation

        // Clear cache to ensure it fetches fresh from DB
        Cache::forget('inventory_locations');

        $options = $this->inventoryService->getDropdownOptions();
        $locations = $options['locations']->toArray();

        $this->assertContains('Tegal', $locations);
        $this->assertContains('Cibubur', $locations);
        $this->assertContains($newLocation, $locations);
    }

    #[Test]
    public function lokasi_default_tidak_duplikat()
    {
        // Simulate creating location 'Tegal' twice as it might happen in concurrent creation
        \App\Models\Location::firstOrCreate(['name' => 'Tegal']);

        Cache::forget('inventory_locations');
        $options = $this->inventoryService->getDropdownOptions();
        $locations = $options['locations']->toArray();

        // Count occurrences of 'Tegal'
        $tegalCount = array_count_values($locations)['Tegal'] ?? 0;
        $this->assertEquals(1, $tegalCount);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_tambah()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('inventory.create'));
        $response->assertStatus(200);
        $response->assertSee('location');
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_tambah()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_halaman_tambah()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.create'));
        $response->assertStatus(403);
    }
}
