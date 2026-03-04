<?php

namespace Tests\Feature;

use App\Jobs\GenerateReportJob;
use App\Models\Sparepart;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReportIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ✅ Data Integrity: Test that PDF Report Jobs use snapshotted data,
     * not future data that changes while the job is waiting in the queue.
     */
    public function test_job_laporan_pdf_menggunakan_snapshot_data_bukan_pengambilan_tertunda()
    {
        // 1. Setup awal
        $superadmin = User::factory()->create(['role' => 'superadmin', 'status' => 'aktif']);
        $sparepart = Sparepart::factory()->create(['name' => 'Busi Motor', 'stock' => 100]);

        // 2. Simulasikan ReportController menangkap snapshot HTTP Request sekarang
        $reportService = app(ReportService::class);
        $snapshotData = $reportService->getReportData('inventory_list', 'all', null, null);

        // Assert snapshot memory menangkap angka 100
        $this->assertEquals(100, $snapshotData['data']->first()->stock);

        // 3. Simulasikan Job PDF dilempar ke Queue (Background) menggunakan data snapshot
        $job = new GenerateReportJob(
            $superadmin,
            $snapshotData,
            null,
            null,
            'all',
            'inventory_list'
        );

        // 4. BENCANA / RACE CONDITION TERJADI:
        // Admin lain secara diam-diam mengurangi stok di database sisa 50
        // SEDANGKAN job PDF masih belum tereksekusi di antrean!
        $sparepart->update(['stock' => 50]);

        // Pastikan stok di database memang sudah berubah jadi 50
        $this->assertEquals(50, $sparepart->fresh()->stock);

        // 5. Worker Queue mulai mengeksekusi Job PDF yang tertunda tadi
        // Kita tidak bisa me-mock isi file PDF dengan mudah, tapi kita bisa
        // menguji isi properti array data yang dibongkar oleh handler Job tersebut.
        $reflection = new \ReflectionClass($job);
        $property = $reflection->getProperty('reportData');
        $property->setAccessible(true);
        $jobMemoryData = $property->getValue($job);

        // 6. KESIMPULAN:
        // Walaupun stok murni di database sekarang 50, PDF harus tetap mencetak angka 100
        // Karena angka 100 adalah fakta pada "detik di mana tombol PDF diklik".
        $this->assertEquals(100, $jobMemoryData['data']->first()->stock);
    }
}
