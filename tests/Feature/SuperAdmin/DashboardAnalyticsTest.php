<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use App\Models\Sparepart;
use App\Models\StockLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2025, 6, 1)); // Freeze time to June 1, 2025
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
    }

    /** @test */
    public function dashboard_shows_correct_basic_stats()
    {
        // Arrange
        Sparepart::factory()->count(3)->create(['stock' => 10]); // Total 30 stock
        Sparepart::factory()->create(['stock' => 5]); // Total 35 stock
        
        // Act
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('totalSpareparts', 4);
        $response->assertViewHas('totalStock', 35);
    }

    /** @test */
    public function dashboard_identifies_dead_stock_correctly()
    {
        // 1. Dead Item: Has stock > 0, created 4 months ago, NO outgoing logs
        $deadItem = Sparepart::factory()->create([
            'name' => 'Dead Item',
            'stock' => 10,
            'created_at' => now()->subMonths(4)
        ]);

        // 2. Active Item: Has outgoing logs recently
        $activeItem = Sparepart::factory()->create(['name' => 'Active Item', 'stock' => 10]);
        StockLog::factory()->create([
            'sparepart_id' => $activeItem->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 1,
            'created_at' => now()->subDays(2)
        ]);

        // Act
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin'));

        // Assert
        $deadStockItems = $response->viewData('deadStockItems');
        $this->assertTrue($deadStockItems->contains($deadItem));
        $this->assertFalse($deadStockItems->contains($activeItem));
    }

    /** @test */
    public function dashboard_forecast_calculation_logic()
    {
        // Arrange: Item with consistent usage over last 3 months
        $item = Sparepart::factory()->create(['stock' => 100]);
        
        // Month 1 ago: 30 used
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 30,
            'created_at' => now()->subMonth()
        ]);
        
        // Month 2 ago: 30 used
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 30,
            'created_at' => now()->subMonths(2)
        ]);

        // Month 3 ago: 30 used
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 30,
            'created_at' => now()->subMonths(3)
        ]);

        // Act
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin', ['period' => 'this_year']));

        // Assert
        // Forecast logic: Average of last 3 months. (30+30+30)/3 = 30
        $forecasts = $response->viewData('forecasts');
        
        // Find our item in forecasts (it should be there because it's a top exited item)
        // Note: The controller logic selects Top Exited items first, then calculates forecast.
        // Since this is the only item with activity, it should be in top 5.
        
        $forecastItem = collect($forecasts)->firstWhere('name', $item->name);
        
        $this->assertNotNull($forecastItem, 'Item should appear in forecast list');
        $this->assertEquals(30, $forecastItem['predicted_need'], 'Prediction should be average of last 3 months');
    }

    /** @test */
    public function dashboard_date_filter_affects_analytics()
    {
        // Arrange
        $item = Sparepart::factory()->create();
        
        // Log from LAST YEAR (Should not appear in "This Year" filter if we filter by current year, 
        // but let's test a specific month filter)
        
        // Log in Jan 2025
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'masuk',
            'status' => 'approved',
            'quantity' => 100,
            'created_at' => Carbon::create(2025, 1, 15)
        ]);
        
        // Log in Feb 2025
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'masuk',
            'status' => 'approved',
            'quantity' => 50,
            'created_at' => Carbon::create(2025, 2, 15)
        ]);



        $this->assertDatabaseCount('stock_logs', 2);
        // Act: Filter for Jan 2025
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin', [
            'year' => 2025,
            'month' => 1
        ]));

        // Assert
        $topEntered = $response->viewData('topEntered');
        
        // Should capture the 100qty, but not the 50qty
        $this->assertEquals(100, $topEntered->first()->total_qty);
    }
}
