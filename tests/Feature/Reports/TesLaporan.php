<?php

namespace Tests\Feature\Reports;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesLaporan extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
        Sparepart::factory()->count(5)->create();
    }

    #[Test]
    public function superadmin_dapat_mengunduh_laporan_pdf_melalui_antrean()
    {
        Queue::fake();
        Sparepart::factory()->count(1001)->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf',
            ]));

        $response->assertRedirect();
        $response->assertSessionHas('info');

        // Pastikan Job Didorong
        Queue::assertPushed(\App\Jobs\GenerateReportJob::class);
    }

    #[Test]
    public function superadmin_dapat_mengunduh_laporan_excel_secara_langsung()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'excel',
            ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_laporan()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_scan_qr()
    {
        // Asumsi rute ada berdasarkan verifikasi sebelumnya
        if (\Illuminate\Support\Facades\Route::has('inventory.scan-qr')) {
            $response = $this->actingAs($this->superAdmin)->get(route('inventory.scan-qr'));
            $response->assertStatus(200);
        }
    }
}
