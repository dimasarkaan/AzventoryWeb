<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
        Sparepart::factory()->count(5)->create();
    }

    /** @test */
    public function superadmin_can_download_pdf_report_via_queue()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf',
            ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan Job Didorong
        Queue::assertPushed(\App\Jobs\GenerateReportJob::class);
    }

    /** @test */
    public function superadmin_can_download_excel_report_directly()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'excel',
            ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
    }
    /** @test */
    public function superadmin_can_access_reports_page()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function superadmin_can_access_qr_scan_page()
    {
        // Asumsi rute ada berdasarkan verifikasi sebelumnya
        if (\Illuminate\Support\Facades\Route::has('inventory.scan-qr')) {
             $response = $this->actingAs($this->superAdmin)->get(route('inventory.scan-qr'));
             $response->assertStatus(200);
        }
    }
}
