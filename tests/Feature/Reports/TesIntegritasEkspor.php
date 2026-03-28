<?php

namespace Tests\Feature\Reports;

use App\Models\Sparepart;
use App\Services\ExcelExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class TesIntegritasEkspor extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Verifikasi konten file Excel yang dihasilkan oleh ExcelExportService.
     */
    public function test_isi_file_excel_inventaris_sesuai_dengan_database()
    {
        if (! class_exists('ZipArchive')) {
            $this->markTestSkipped('Ekstensi PHP ZipArchive tidak tersedia di lingkungan ini (Windows).');
        }

        $spareparts = Sparepart::factory()->count(3)->create([
            'category' => 'Testing Category',
            'location' => 'Rak Test',
        ]);

        $service = new ExcelExportService;

        // Clean up any old test files first
        $oldFiles = glob(storage_path('app/public/reports/temp_*_test_export.xlsx'));
        foreach ($oldFiles as $file) { @unlink($file); }

        $response = $service->exportInventoryList($spareparts, 'test_export');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));

        // Find the specific file we just created
        $files = glob(storage_path('app/public/reports/temp_*_test_export.xlsx'));
        $filePath = $files[0] ?? null;

        $this->assertNotNull($filePath, 'Excel file was not generated in storage/app/public/reports');
        $this->assertFileExists($filePath);

        // Load spreadsheet and verify content
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header title check (should be uppercase)
        $this->assertEquals('LAPORAN DAFTAR INVENTARIS (SPAREPART & ASET)', $sheet->getCell('A1')->getValue());
        
        // Data check (Category is in Column C, Row 6)
        $this->assertEquals('Testing Category', $sheet->getCell('C6')->getValue());

        // Explicitly clear memory and collect cycles to help Windows release locks
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        unset($sheet);
        gc_collect_cycles();

        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}

