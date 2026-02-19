<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
    }

    // Menguji perhitungan rentang tanggal dengan benar.
    /** @test */
    public function it_calculates_date_range_correctly()
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
    /** @test */
    public function it_returns_stock_snapshots()
    {
        Sparepart::factory()->count(3)->create(['stock' => 10]);
        
        $snapshots = $this->service->getStockSnapshots();
        
        $this->assertEquals(3, $snapshots['totalSpareparts']);
        $this->assertEquals(30, $snapshots['totalStock']);
    }

    // Menguji pengambilan data pergerakan stok.
    /** @test */
    public function it_returns_stock_movements()
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
            'created_at' => Carbon::today()
        ]);
        
        StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'type' => 'keluar',
            'quantity' => 5,
            'status' => 'approved',
            'reason' => 'Test',
            'created_at' => Carbon::today()
        ]);

        $start = Carbon::today();
        $end = Carbon::tomorrow();
        
        $data = $this->service->getStockMovements($start, $end);
        
        $this->assertCount(1, $data['labels']);
        $this->assertEquals([10], $data['masuk']);
        $this->assertEquals([5], $data['keluar']);
    }

     // Menguji pengambilan item terlaris.
     /** @test */
    public function it_returns_top_items()
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
}
