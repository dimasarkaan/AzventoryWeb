<?php

namespace Tests\Unit\Inventory;

use App\Models\Sparepart;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit test untuk QrCodeService.
 * Memastikan QR Code berhasil digenerate dan disimpan ke storage.
 */
class TesServisQrCode extends TestCase
{
    use RefreshDatabase;

    protected QrCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->service = new QrCodeService;
    }

    #[Test]
    public function generate_menghasilkan_file_qr_code_di_storage()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'QR-TEST-001']);

        $path = $this->service->generate($sparepart);

        Storage::disk('public')->assertExists($path);
        $this->assertStringContainsString('qrcodes/', $path);
        $this->assertStringContainsString('QR-TEST-001', $path);
    }

    #[Test]
    public function generate_menyimpan_path_qr_code_ke_model_sparepart()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'QR-TEST-002']);

        $this->service->generate($sparepart);

        $this->assertNotNull($sparepart->fresh()->qr_code_path);
        $this->assertStringContainsString('QR-TEST-002', $sparepart->fresh()->qr_code_path);
    }

    #[Test]
    public function get_label_filename_menghasilkan_nama_file_yang_benar()
    {
        $sparepart = Sparepart::factory()->make([
            'category' => 'Filter',
            'brand' => 'Toyota',
            'part_number' => 'FT-999',
        ]);

        $filename = $this->service->getLabelFilename($sparepart);

        $this->assertStringContainsString('Filter', $filename);
        $this->assertStringContainsString('Toyota', $filename);
        $this->assertStringContainsString('FT-999', $filename);
        $this->assertStringEndsWith('.svg', $filename);
    }

    #[Test]
    public function generate_label_svg_menghasilkan_konten_svg_yang_valid()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'LABEL-001']);
        $this->service->generate($sparepart);

        $svg = $this->service->generateLabelSvg($sparepart);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('LABEL-001', $svg);
    }

    #[Test]
    public function generate_label_svg_meregenerasi_qr_jika_file_hilang()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'REGEN-001']);
        // Sengaja tidak generate QR terlebih dahulu - qr_code_path = null
        $this->assertNull($sparepart->qr_code_path);

        $svg = $this->service->generateLabelSvg($sparepart);

        // Harus tetap mengembalikan SVG valid meskipun QR belum ada
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('REGEN-001', $svg);
    }
}
