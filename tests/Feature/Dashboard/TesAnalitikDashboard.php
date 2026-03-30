<?php

namespace Tests\Feature\Dashboard;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesAnalitikDashboard extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Cache::flush();
        Carbon::setTestNow(Carbon::create(2025, 6, 1)); // Bekukan waktu ke 1 Juni 2025
        $this->superAdmin = User::factory()->create(['role' => 'superadmin', 'status' => 'aktif']);
    }

    #[Test]
    public function dashboard_menampilkan_statistik_dasar_dengan_benar()
    {
        // Persiapan
        Sparepart::factory()->count(3)->create(['stock' => 10]); // Total 30 stok
        Sparepart::factory()->create(['stock' => 5]); // Total 35 stok

        // Aksi
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin'));

        // Verifikasi
        $response->assertStatus(200);
        $response->assertViewHas('totalSpareparts', 4);
        $response->assertViewHas('totalStock', 35);
    }

    #[Test]
    public function dashboard_mengidentifikasi_stok_mati_dengan_benar()
    {
        // 1. Item Mati: Stok > 0, dibuat 4 bulan lalu, TIDAK ada log keluar
        $deadItem = Sparepart::factory()->create([
            'name' => 'Dead Item',
            'stock' => 10,
            'created_at' => now()->subMonths(4),
        ]);

        // 2. Item Aktif: Ada log keluar baru-baru ini
        $activeItem = Sparepart::factory()->create(['name' => 'Active Item', 'stock' => 10]);
        StockLog::factory()->create([
            'sparepart_id' => $activeItem->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 1,
            'created_at' => now()->subDays(2),
        ]);

        // Act
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin'));

        // Assert
        $deadStockItems = $response->viewData('deadStockItems');
        $this->assertTrue($deadStockItems->contains($deadItem));
        $this->assertFalse($deadStockItems->contains($activeItem));
    }

    #[Test]
    public function dashboard_logika_perhitungan_perkiraan_stok()
    {
        // Persiapan: Item dengan penggunaan konsisten selama 3 bulan terakhir
        $item = Sparepart::factory()->create(['stock' => 100]);

        // 1 Bulan lalu: 30 terpakai
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 30,
            'created_at' => now()->subMonth(),
        ]);

        // 2 Bulan lalu: 30 terpakai
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 30,
            'created_at' => now()->subMonths(2),
        ]);

        // 3 Bulan lalu: 30 terpakai
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'keluar',
            'status' => 'approved',
            'quantity' => 30,
            'created_at' => now()->subMonths(3),
        ]);

        // Act
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin', ['period' => 'this_year']));

        // Assert
        // Logika Perkiraan: Rata-rata 3 bulan terakhir. (30+30+30)/3 = 30
        $forecasts = $response->viewData('forecasts');

        // Temukan item kita dalam perkiraan (seharusnya ada karena item keluar teratas)
        // Catatan: Logika controller memilih item Keluar Teratas dulu, lalu hitung perkiraan.
        // Karena ini satu-satunya item dengan aktivitas, harusnya ada di top 5.

        $forecastItem = collect($forecasts)->firstWhere('name', $item->name);

        $this->assertNotNull($forecastItem, 'Item should appear in forecast list');
        $this->assertEquals(30, $forecastItem['predicted_need'], 'Prediction should be average of last 3 months');
    }

    #[Test]
    public function dashboard_filter_tanggal_mempengaruhi_analitik()
    {
        // Persiapan
        $item = Sparepart::factory()->create();

        // Log dari TAHUN LALU (Seharusnya tidak muncul di filter "Tahun Ini" jika kita filter berdasarkan tahun saat ini,
        // tapi mari kita tes filter bulan spesifik)

        // Log in Jan 2025
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'masuk',
            'status' => 'approved',
            'quantity' => 100,
            'created_at' => Carbon::create(2025, 1, 15),
        ]);

        // Log in Feb 2025
        StockLog::factory()->create([
            'sparepart_id' => $item->id,
            'type' => 'masuk',
            'status' => 'approved',
            'quantity' => 50,
            'created_at' => Carbon::create(2025, 2, 15),
        ]);

        // Aksi: Filter untuk Jan 2025
        $service = app(\App\Services\DashboardService::class);
        $topEntered = $service->getTopItems(
            Carbon::create(2025, 1, 1)->startOfMonth(),
            Carbon::create(2025, 1, 1)->endOfMonth(),
            'masuk'
        );

        // Assert
        $this->assertFalse($topEntered->isEmpty(), 'Top entered items should not be empty');
        $this->assertEquals(100, (int) $topEntered->first()->total_qty);
    }

    #[Test]
    public function dashboard_memvalidasi_tanggal_akhir_setelah_atau_sama_dengan_tanggal_mulai()
    {
        // Act: Kirim end_date < start_date
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin', [
            'start_date' => '2025-06-10',
            'end_date' => '2025-06-05',
        ]), ['Accept' => 'application/json']);

        // Assert: Harus gagal validasi
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }

    #[Test]
    public function dashboard_memvalidasi_rentang_tanggal_maksimal_365_hari()
    {
        // Act: Kirim rentang > 365 hari (2 tahun)
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin', [
            'start_date' => '2024-01-01',
            'end_date' => '2025-01-02', // 367 hari
        ]), ['Accept' => 'application/json']);

        // Assert: Harus gagal validasi
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }
}
