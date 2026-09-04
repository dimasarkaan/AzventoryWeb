<?php

namespace Tests\Feature\Reports;

use App\Enums\UserRole;
use App\Jobs\GenerateReportJob;
use App\Models\Sparepart;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TesLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);

        $this->operator = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_halaman_index_laporan_bisa_dibuka()
    {
        $response = $this->actingAs($this->superadmin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_download_pdf_langsung_jika_data_sedikit()
    {
        Sparepart::factory()->count(10)->create(); // 10 data (<= 1000)

        // Mock PDF biar gak beneran render DOMPDF yang berat saat testing
        Pdf::shouldReceive('loadView')->andReturnSelf();
        Pdf::shouldReceive('output')->andReturn('PDF_CONTENT_MOCK');

        $response = $this->actingAs($this->superadmin)->get(route('reports.download', [
            'report_type' => 'inventory_list',
            'export_format' => 'pdf',
            'period' => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_download_pdf_memicu_queue_jika_data_raksasa()
    {
        Queue::fake();

        Sparepart::factory()->count(1001)->create(); // 1001 data (> 1000)

        $response = $this->actingAs($this->superadmin)->get(route('reports.download', [
            'report_type' => 'inventory_list',
            'export_format' => 'pdf',
            'period' => 'all',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('info');

        Queue::assertPushed(GenerateReportJob::class);
    }

    public function test_download_excel_mengembalikan_respon_valid()
    {
        Sparepart::factory()->count(10)->create();

        $response = $this->actingAs($this->superadmin)->get(route('reports.download', [
            'report_type' => 'inventory_list',
            'export_format' => 'excel',
            'period' => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertDownload();
    }

    public function test_download_via_secure_file_link()
    {
        // Simulasikan ada file di disk local
        Storage::disk('local')->put('reports/LaporanTesting.pdf', 'dummy content');

        $response = $this->actingAs($this->superadmin)->get(route('reports.file', ['filename' => 'LaporanTesting.pdf']));

        $response->assertStatus(200);
        $response->assertDownload();
    }

    public function test_validasi_menolak_end_date_sebelum_start_date()
    {
        $response = $this->actingAs($this->superadmin)->get(route('reports.download', [
            'start_date' => '2023-12-01',
            'end_date' => '2023-11-01', // Kuno
        ]));

        $response->assertSessionHasErrors('end_date');
    }

    public function test_mendownload_file_hilang_mengembalikan_error()
    {
        // File tidak ada di local ataupun public
        $response = $this->actingAs($this->superadmin)->get(route('reports.file', ['filename' => 'FileHantu.pdf']));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_tipe_laporan_tidak_didukung_untuk_excel()
    {
        $response = $this->actingAs($this->superadmin)->get(route('reports.download', [
            'report_type' => 'alien_type',
            'export_format' => 'excel',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tipe laporan tidak ditemukan atau tidak valid.');
    }
}
