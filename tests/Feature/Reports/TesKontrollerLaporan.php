<?php

namespace Tests\Feature\Reports;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk ReportController.
 * Mencakup semua tipe laporan, format PDF vs Excel, dan pembatasan akses.
 */
class TesKontrollerLaporan extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    // ── index ────────────────────────────────────────────────────

    #[Test]
    public function superadmin_dapat_mengakses_halaman_laporan()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.index'));

        $response->assertOk();
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_laporan()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.index'));

        $response->assertOk();
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_halaman_laporan()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('reports.index'));

        $response->assertForbidden();
    }

    // ── PDF dispatch ─────────────────────────────────────────────

    #[Test]
    public function download_pdf_mendispatch_job_dan_mengembalikan_pesan_sukses()
    {
        Queue::fake();
        Sparepart::factory()->count(1001)->create();

        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf',
                'period' => 'all',
            ]));

        $response->assertRedirect();
        $response->assertSessionHas('info');
        Queue::assertPushed(\App\Jobs\GenerateReportJob::class);
    }

    #[Test]
    public function download_pdf_langsung_berhasil_tanpa_antrean()
    {
        // Jangan Queue::fake() agar kita bisa mengetes alur sinkron
        Sparepart::factory()->count(5)->create();

        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf',
                'period' => 'all',
            ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    // ── Excel exports ────────────────────────────────────────────

    #[Test]
    public function download_excel_inventory_list_mengembalikan_file_excel()
    {
        if (! class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive tidak tersedia.');
        }

        Sparepart::factory()->count(2)->create();

        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'excel',
                'period' => 'all',
            ]));

        $response->assertOk();
        $this->assertStringContainsString(
            'spreadsheet',
            strtolower($response->headers->get('Content-Type', ''))
        );
    }

    #[Test]
    public function download_excel_stock_mutation_mengembalikan_file()
    {
        if (! class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive tidak tersedia.');
        }

        $response = $this->actingAs($this->admin)
            ->get(route('reports.download', [
                'report_type' => 'stock_mutation',
                'export_format' => 'excel',
                'period' => 'all',
            ]));

        $response->assertOk();
    }

    #[Test]
    public function download_excel_borrowing_history_mengembalikan_file()
    {
        if (! class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive tidak tersedia.');
        }

        $response = $this->actingAs($this->admin)
            ->get(route('reports.download', [
                'report_type' => 'borrowing_history',
                'export_format' => 'excel',
                'period' => 'all',
            ]));

        $response->assertOk();
    }

    #[Test]
    public function download_excel_low_stock_mengembalikan_file()
    {
        if (! class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive tidak tersedia.');
        }

        Sparepart::factory()->create(['stock' => 1, 'minimum_stock' => 10, 'condition' => 'Baik']);

        $response = $this->actingAs($this->admin)
            ->get(route('reports.download', [
                'report_type' => 'low_stock',
                'export_format' => 'excel',
                'period' => 'all',
            ]));

        $response->assertOk();
    }

    // ── Activity Log Export ──────────────────────────────────────

    #[Test]
    public function superadmin_dapat_mengakses_halaman_activity_log()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.activity-logs.index'));

        $response->assertOk();
    }

    #[Test]
    public function export_activity_log_pdf_mendispatch_job()
    {
        Queue::fake();
        ActivityLog::factory()->count(501)->create();

        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.activity-logs.export', ['format' => 'pdf']));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Queue::assertPushed(\App\Jobs\ExportActivityLogJob::class);
    }

    #[Test]
    public function export_activity_log_excel_mengembalikan_file()
    {
        if (! class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive tidak tersedia.');
        }

        ActivityLog::factory()->count(2)->create([
            'user_id' => $this->superadmin->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.activity-logs.export', ['format' => 'excel']));

        $response->assertOk();
    }

    #[Test]
    public function download_dengan_tanggal_terbalik_mengembalikan_error_validasi()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.download', [
                'report_type' => 'stock_mutation',
                'export_format' => 'pdf',
                'period' => 'custom',
                'start_date' => '2026-03-10',
                'end_date' => '2026-03-01',
            ]));

        $response->assertSessionHasErrors(['end_date']);
    }

    #[Test]
    public function download_pdf_menampilkan_pesan_sukses_baru()
    {
        Queue::fake();
        Sparepart::factory()->count(1001)->create();
        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'pdf',
                'period' => 'all',
            ]));

        $response->assertSessionHas('info', 'Laporan sedang diproses karena ukuran data yang besar. Silakan cek menu Notifikasi dalam beberapa saat untuk mengunduh file.');
    }
}

