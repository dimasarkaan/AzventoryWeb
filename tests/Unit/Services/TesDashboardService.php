<?php

namespace Tests\Unit\Services;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesDashboardService extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService;
    }

    // Menguji perhitungan rentang tanggal dengan benar.
    #[Test]
    public function menghitung_rentang_tanggal_dengan_benar()
    {
        // Test Hari Ini
        [$start, $end, $period] = $this->service->getDateRange('today', null, null);
        $this->assertEquals(Carbon::today(), $start);
        $this->assertEquals(Carbon::tomorrow(), $end);
        $this->assertEquals('today', $period);

        // Test Bulan Spesifik
        [$start, $end, $period] = $this->service->getDateRange(null, '2025', '5');
        $this->assertEquals(Carbon::create(2025, 5, 1)->startOfMonth(), $start);
        $this->assertEquals(Carbon::create(2025, 5, 1)->endOfMonth(), $end);
        $this->assertEquals('custom', $period);
    }

    // Menguji pengambilan snapshot stok.
    #[Test]
    public function mengembalikan_snapshot_stok_dengan_benar()
    {
        Sparepart::factory()->count(3)->create(['stock' => 10]);
        \App\Models\Brand::insert([
            ['name' => 'Brand A'],
            ['name' => 'Brand B'],
        ]);
        \App\Models\Category::insert([
            ['name' => 'Cat A'],
            ['name' => 'Cat B'],
            ['name' => 'Cat C'],
            ['name' => 'Cat D'],
        ]);

        $snapshots = $this->service->getStockSnapshots();

        $this->assertEquals(3, $snapshots['totalSpareparts']);
        $this->assertEquals(30, $snapshots['totalStock']);
        $this->assertEquals(2, $snapshots['totalBrands']);
        $this->assertEquals(4, $snapshots['totalCategories']);
    }

    // Menguji pengambilan data pergerakan stok.
    #[Test]
    public function mengembalikan_pergerakan_stok_dengan_benar()
    {
        $sparepart = Sparepart::factory()->create();
        $user = User::factory()->create();

        // Create stock logs
        StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'type' => 'masuk',
            'quantity' => 10,
            'status' => 'approved',
            'reason' => 'Test',
            'created_at' => Carbon::today(),
        ]);

        StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'type' => 'keluar',
            'quantity' => 5,
            'status' => 'approved',
            'reason' => 'Test',
            'created_at' => Carbon::today(),
        ]);

        $start = Carbon::today();
        $end = Carbon::tomorrow();

        $data = $this->service->getStockMovements($start, $end);

        $this->assertCount(1, $data['labels']);
        $this->assertEquals([10], $data['masuk']);
        $this->assertEquals([5], $data['keluar']);
    }

    // Menguji pengambilan item terlaris.
    #[Test]
    public function mengembalikan_item_teratas_dengan_benar()
    {
        $sparepart1 = Sparepart::factory()->create(['name' => 'Item A']);
        $sparepart2 = Sparepart::factory()->create(['name' => 'Item B']);
        $user = User::factory()->create();

        // Item A exited 20
        StockLog::create([
            'sparepart_id' => $sparepart1->id,
            'user_id' => $user->id,
            'type' => 'keluar',
            'quantity' => 20,
            'status' => 'approved',
            'reason' => 'Test',
        ]);

        // Item B exited 5
        StockLog::create([
            'sparepart_id' => $sparepart2->id,
            'user_id' => $user->id,
            'type' => 'keluar',
            'quantity' => 5,
            'status' => 'approved',
            'reason' => 'Test',
        ]);

        $start = Carbon::yesterday();
        $end = Carbon::tomorrow();

        $topItems = $this->service->getTopItems($start, $end, 'keluar');

        $this->assertCount(2, $topItems);
        $this->assertEquals('Item A', $topItems->first()->sparepart_name);
        $this->assertEquals(20, $topItems->first()->total_qty);
    }

    // ── Regression: minimum_stock NULL ───────────────────────────

    #[Test]
    public function snapshot_low_stock_tidak_include_item_tanpa_minimum_stock()
    {
        // Database minimum_stock adalah NOT NULL default 0.
        // Item dengan minimum_stock = 0 dan stock = 0 TIDAK boleh masuk lowStockItems.
        Sparepart::factory()->create([
            'stock' => 0,
            'minimum_stock' => 0,
            'condition' => 'Baik',
        ]);

        $snapshots = $this->service->getStockSnapshots();

        $this->assertCount(0, $snapshots['lowStockItems']);
    }

    #[Test]
    public function snapshot_low_stock_tidak_include_item_minimum_nol()
    {
        Sparepart::factory()->create([
            'stock' => 0,
            'minimum_stock' => 0,
            'condition' => 'Baik',
        ]);

        $snapshots = $this->service->getStockSnapshots();

        $this->assertCount(0, $snapshots['lowStockItems']);
    }

    #[Test]
    public function snapshot_low_stock_include_item_yang_benar_benar_kritis()
    {
        Sparepart::factory()->create(['stock' => 2,  'minimum_stock' => 10,   'condition' => 'Baik']); // ✅
        Sparepart::factory()->create(['stock' => 0,  'minimum_stock' => 0,    'condition' => 'Baik']); // skip
        Sparepart::factory()->create(['stock' => 50, 'minimum_stock' => 10,   'condition' => 'Baik']); // skip

        $snapshots = $this->service->getStockSnapshots();

        $this->assertCount(1, $snapshots['lowStockItems']);
        $this->assertEquals(2, $snapshots['lowStockItems']->first()->stock);
    }

    #[Test]
    public function recent_activities_menghormati_parameter_limit()
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 6; $i++) {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'test',
                'description' => "Activity {$i}",
                'created_at' => Carbon::now()->subMinutes($i),
            ]);
        }

        // Default limit = 3
        $this->assertCount(3, $this->service->getRecentActivities());

        // Custom limit = 6
        $this->assertCount(6, $this->service->getRecentActivities(null, 6));
    }
}
