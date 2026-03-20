<?php

namespace Tests\Unit\Services;

use App\Models\Sparepart;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * White-box unit test untuk InventoryService.
 * Memverifikasi filtering, sorting, dan logika bisnis utama.
 */
class TesServisInventaris extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(InventoryService::class);
    }

    // ── getFilteredSpareparts ────────────────────────────────────

    #[Test]
    public function filter_pencarian_menemukan_sparepart_berdasarkan_nama()
    {
        Sparepart::factory()->create(['name' => 'Baut Khusus M8']);
        Sparepart::factory()->create(['name' => 'Oli Mesin 5W30']);

        $result = $this->service->getFilteredSpareparts(['search' => 'Baut']);

        $this->assertCount(1, $result);
        $this->assertEquals('Baut Khusus M8', $result->first()->name);
    }

    #[Test]
    public function filter_pencarian_menemukan_sparepart_berdasarkan_part_number()
    {
        Sparepart::factory()->create(['part_number' => 'BK-999-X']);
        Sparepart::factory()->create(['part_number' => 'OL-001-Y']);

        $result = $this->service->getFilteredSpareparts(['search' => 'BK-999']);

        $this->assertCount(1, $result);
        $this->assertEquals('BK-999-X', $result->first()->part_number);
    }

    #[Test]
    public function filter_kategori_menampilkan_hanya_sparepart_kategori_tertentu()
    {
        Sparepart::factory()->create(['category' => 'Filter']);
        Sparepart::factory()->create(['category' => 'Oli']);
        Sparepart::factory()->create(['category' => 'Oli']);

        $result = $this->service->getFilteredSpareparts(['category' => 'Oli']);

        $this->assertCount(2, $result);
        foreach ($result as $item) {
            $this->assertEquals('Oli', $item->category);
        }
    }

    #[Test]
    public function filter_low_stock_hanya_menampilkan_stok_di_bawah_minimum()
    {
        Sparepart::factory()->create(['stock' => 1, 'minimum_stock' => 10, 'condition' => 'Baik']);
        Sparepart::factory()->create(['stock' => 50, 'minimum_stock' => 10, 'condition' => 'Baik']);
        Sparepart::factory()->create(['stock' => 5, 'minimum_stock' => 5, 'condition' => 'Baik']); // tepat di batas

        $result = $this->service->getFilteredSpareparts(['filter' => 'low_stock']);

        $this->assertCount(2, $result);
    }

    #[Test]
    public function filter_no_price_menampilkan_sparepart_tanpa_harga()
    {
        Sparepart::factory()->create(['price' => null, 'type' => 'sale']);
        Sparepart::factory()->create(['price' => 0, 'type' => 'sale']);
        Sparepart::factory()->create(['price' => 50000, 'type' => 'sale']);

        $result = $this->service->getFilteredSpareparts(['filter' => 'no_price']);

        $this->assertCount(2, $result);
    }

    #[Test]
    public function filter_trash_menampilkan_hanya_data_yang_sudah_dihapus()
    {
        $normal = Sparepart::factory()->create(['name' => 'Active Item']);
        $deleted = Sparepart::factory()->create(['name' => 'Deleted Item']);
        $deleted->delete();

        $result = $this->service->getFilteredSpareparts(['trash' => 'true']);

        $this->assertCount(1, $result);
        $this->assertEquals('Deleted Item', $result->first()->name);
    }

    #[Test]
    public function sorting_berdasarkan_stok_ascending_bekerja()
    {
        Sparepart::factory()->create(['stock' => 50]);
        Sparepart::factory()->create(['stock' => 5]);
        Sparepart::factory()->create(['stock' => 100]);

        $result = $this->service->getFilteredSpareparts(['sort' => 'stock_asc']);

        $stocks = $result->pluck('stock')->toArray();
        $this->assertEquals([5, 50, 100], $stocks);
    }

    #[Test]
    public function sorting_berdasarkan_stok_descending_bekerja()
    {
        Sparepart::factory()->create(['stock' => 50]);
        Sparepart::factory()->create(['stock' => 5]);
        Sparepart::factory()->create(['stock' => 100]);

        $result = $this->service->getFilteredSpareparts(['sort' => 'stock_desc']);

        $stocks = $result->pluck('stock')->toArray();
        $this->assertEquals([100, 50, 5], $stocks);
    }

    // ── getDropdownOptions ───────────────────────────────────────

    #[Test]
    public function get_dropdown_options_mengembalikan_daftar_kategori_unik()
    {
        Cache::flush();
        \App\Models\Category::firstOrCreate(['name' => 'Filter']);
        \App\Models\Category::firstOrCreate(['name' => 'Oli']);
        \App\Models\Category::firstOrCreate(['name' => 'Oli']); // duplikat

        $options = $this->service->getDropdownOptions();

        $this->assertArrayHasKey('categories', $options);
        // categories hanya nilai unik
        $this->assertCount(2, $options['categories']);
    }

    // ── Regression: minimum_stock NULL ───────────────────────────

    #[Test]
    public function filter_low_stock_tidak_include_item_tanpa_minimum_stock()
    {
        // Database minimum_stock adalah NOT NULL default 0.
        // Item dengan minimum_stock = 0 harus TIDAK muncul di filter low_stock.
        Sparepart::factory()->create([
            'stock' => 0,
            'minimum_stock' => 0, // 0 = tidak dipantau
            'condition' => 'Baik',
        ]);

        $result = $this->service->getFilteredSpareparts(['filter' => 'low_stock']);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function filter_low_stock_tidak_include_item_dengan_minimum_stock_nol()
    {
        // minimum_stock = 0 juga tidak dipantau (sama dengan tidak diisi)
        Sparepart::factory()->create([
            'stock' => 0,
            'minimum_stock' => 0,
            'condition' => 'Baik',
        ]);

        $result = $this->service->getFilteredSpareparts(['filter' => 'low_stock']);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function filter_low_stock_hanya_include_item_dengan_minimum_stock_lebih_dari_nol_dan_stok_kurang()
    {
        Sparepart::factory()->create(['stock' => 0,  'minimum_stock' => 0,    'condition' => 'Baik']); // 0 → skip
        Sparepart::factory()->create(['stock' => 3,  'minimum_stock' => 10,   'condition' => 'Baik']); // ✅ masuk
        Sparepart::factory()->create(['stock' => 50, 'minimum_stock' => 10,   'condition' => 'Baik']); // stok cukup → skip

        $result = $this->service->getFilteredSpareparts(['filter' => 'low_stock']);

        $this->assertCount(1, $result);
        $this->assertEquals(3, $result->first()->stock);
    }
}

