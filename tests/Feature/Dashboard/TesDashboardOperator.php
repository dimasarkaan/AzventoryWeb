<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesDashboardOperator extends TestCase
{
    use RefreshDatabase;

    public function test_operator_dapat_mengakses_halaman_dashboard()
    {
        $operator = User::factory()->create([
            'role' => \App\Enums\UserRole::OPERATOR,
        ]);

        $response = $this->actingAs($operator)->get('/dashboard/operator');

        $response->assertStatus(200);
        $response->assertSee('Halo, '.$operator->name);
    }

    public function test_dashboard_operator_memiliki_fab_scan_qr_dan_modal_lengkap()
    {
        $operator = User::factory()->create([
            'role' => \App\Enums\UserRole::OPERATOR,
        ]);

        $response = $this->actingAs($operator)->get('/dashboard/operator');

        $response->assertStatus(200);

        // Verifikasi FAB Scan QR Utama ada di halaman
        $response->assertSeeText('Scan QR');
        $response->assertSee('id="start-scan-btn"', false);

        // Verifikasi Modal Kamera ada
        $response->assertSee('id="qr-reader-modal"', false);
        $response->assertSeeText('Scan Barcode / QR Barang');

        // Verifikasi Fungsi Kamera
        $response->assertSee('id="switch-camera-btn"', false);
        $response->assertSeeText('Putar Kamera');

        // Verifikasi Fungsi Upload Galeri
        $response->assertSee('id="qr-input-file"', false);
        $response->assertSeeText('Upload Galeri');
    }
}
