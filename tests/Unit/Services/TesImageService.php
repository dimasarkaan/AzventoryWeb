<?php

namespace Tests\Unit\Services;

use App\Services\ImageOptimizationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TesImageService extends TestCase
{
    /**
     * Test: ImageOptimizationService mengonversi gambar ke WebP dan menyimpannya.
     */
    public function test_optimize_and_save_mengonversi_ke_webp()
    {
        Storage::fake('public');

        $service = new ImageOptimizationService;

        // Buat dummy image (JPEG)
        $file = UploadedFile::fake()->image('test_product.jpg', 3000, 2000);

        $path = $service->optimizeAndSave($file, 'inventory');

        // Verifikasi path ada dan berekstensi .webp
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        // Verifikasi dimensi di-resize (max width 1000)
        $savedFile = Storage::disk('public')->path($path);
        $size = getimagesize($savedFile);

        $this->assertEquals(1000, $size[0]); // Width should be resized to 1000
        $this->assertEquals('image/webp', $size['mime']);
    }

    /**
     * Test: Gambar yang ukurannya sudah kecil tidak perlu di-resize lebarnya.
     */
    public function test_gambar_kecil_tidak_di_resize_dimensinya()
    {
        Storage::fake('public');

        $service = new ImageOptimizationService;

        $file = UploadedFile::fake()->image('small.png', 500, 500);

        $path = $service->optimizeAndSave($file, 'inventory');

        $savedFile = Storage::disk('public')->path($path);
        $size = getimagesize($savedFile);

        $this->assertEquals(500, $size[0]); // Tetap 500
        $this->assertEquals('image/webp', $size['mime']);
    }
}
