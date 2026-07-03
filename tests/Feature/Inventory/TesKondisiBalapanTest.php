<?php

namespace Tests\Feature\Inventory;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TesKondisiBalapanTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\Brand */
    protected $brand;

    /** @var \App\Models\Category */
    protected $category;

    /** @var \App\Models\Location */
    protected $location;

    /** @var \App\Models\User */
    protected $superadmin;

    /** @var \App\Models\User */
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Mempersiapkan data master yang dibutuhkan
        $this->brand = Brand::factory()->create(['name' => 'Yamaha']);
        $this->category = Category::factory()->create(['name' => 'Mesin']);
        $this->location = Location::factory()->create(['name' => 'Rak A']);

        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * Membuktikan bahwa pembuatan barang secara serentak tidak menghasilkan duplikat.
     */
    public function test_mencegah_duplikasi_saat_pembuatan_barang_serentak()
    {
        // Simulasi data barang baru
        $dataBarang = [
            'part_number' => 'PN-RACE-01',
            'name' => 'Oli Sintetis 1L',
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'location_id' => $this->location->id,
            'age' => 'Baru',
            'condition' => 'Baik',
            'type' => 'sale',
            'stock' => 10,
            'status' => 'aktif',
        ];

        // Simulasi 2 Request masuk secara bersamaan dalam mode asynchronous
        // Karena PHPUnit berjalan sinkron, kita tes perilaku Atomic Lock dengan mensimulasikan lock telah diambil.

        $lockKey = 'create_sparepart_'.md5(
            $dataBarang['part_number'].
            $dataBarang['name'].
            $dataBarang['brand_id'].
            $dataBarang['category_id'].
            $dataBarang['location_id'].
            $dataBarang['condition'].
            $dataBarang['type']
        );

        // Kunci gembok (seolah-olah ada request lain yang sedang jalan)
        Cache::lock($lockKey, 5)->get();

        $response = $this->actingAs($this->admin)->post(route('inventory.store'), $dataBarang);

        // Permintaan kedua harus ditolak atau diarahkan kembali dengan pesan peringatan race condition
        $response->assertSessionHas('warning');

        // Memastikan barang belum terbuat karena terkunci
        $this->assertDatabaseCount('spareparts', 0);

        // Lepaskan kunci
        Cache::lock($lockKey, 5)->forceRelease();

        // Coba lagi saat tidak terkunci
        $responseSuccess = $this->actingAs($this->admin)->post(route('inventory.store'), $dataBarang);

        // Harusnya sukses
        $responseSuccess->assertSessionHas('success');
        $this->assertDatabaseCount('spareparts', 1);
    }

    /**
     * Membuktikan bahwa pemotongan stok via API aman dari stok negatif.
     */
    public function test_mencegah_stok_negatif_saat_pemindaian_serentak()
    {
        // Buat barang dengan sisa stok 1
        $sparepart = Sparepart::factory()->create([
            'stock' => 1,
            'part_number' => 'PN-SCAN-01',
        ]);

        // Karena test berjalan sinkron, kita akan memverifikasi bahwa DB transaction dan lockForUpdate
        // mencegah stok turun di bawah 0 jika permintaan lebih dari sisa.
        // Simulasi pemotongan stok sebanyak 2 (meskipun sisa 1)

        $response = $this->actingAs($this->admin)->putJson(route('api.inventory.adjust-stock', $sparepart->uuid), [
            'type' => 'decrement',
            'quantity' => 2,
            'description' => 'Test scan ganda',
        ]);

        // Harusnya ditolak dengan status 400
        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Insufficient stock',
        ]);

        // Stok tidak boleh berkurang
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 1,
        ]);

        // Test pemotongan stok normal yang diizinkan (quantity 1)
        $responseNormal = $this->actingAs($this->admin)->putJson(route('api.inventory.adjust-stock', $sparepart->uuid), [
            'type' => 'decrement',
            'quantity' => 1,
            'description' => 'Test scan valid',
        ]);

        $responseNormal->assertStatus(200);

        // Stok menjadi 0 (Depleted)
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 0,
        ]);
    }
}
