<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * DownloadEndpointTest — cover endpoint file download dan AJAX utility yang belum ter-cover.
 *
 * Coverage baru (tidak ada di file lain):
 * - QR code download (SVG) — inventory.qr.download
 * - QR code print page  — inventory.qr.print
 * - check-part-number   — inventory.check-part-number (AJAX JSON)
 *
 * TIDAK MENCAKUP (sudah ada di ReportControllerTest.php):
 * - reports.download (Excel/PDF)
 * - reports.activity-logs.export (Excel/PDF)
 */
class TesEndpointUnduh extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $admin;
    protected User $operator;
    protected Sparepart $sparepart;
    protected Sparepart $sparepartWithQr;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role'               => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);
        $this->admin = User::factory()->create([
            'role'               => UserRole::ADMIN,
            'password_changed_at' => now(),
        ]);
        $this->operator = User::factory()->create([
            'role'               => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        // Sparepart biasa — tanpa QR code
        $this->sparepart = Sparepart::factory()->create(['stock' => 5]);

        // Sparepart dengan qr_code_path — untuk test print label
        $this->sparepartWithQr = Sparepart::factory()->create([
            'stock'        => 5,
            'qr_code_path' => 'qr_codes/test_qr.svg',
        ]);
    }

    // =========================================================================
    // QR CODE DOWNLOAD (SVG file)
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mengunduh_qr_code_sebagai_svg()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.download', $this->sparepart->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $response->assertHeader('Content-Disposition');
    }

    #[Test]
    public function admin_dapat_mengunduh_qr_code_sebagai_svg()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('inventory.qr.download', $this->sparepart->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    #[Test]
    public function operator_dapat_mengunduh_qr_code_sebagai_svg()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.qr.download', $this->sparepart->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    #[Test]
    public function guest_tidak_dapat_mengunduh_qr_code()
    {
        $response = $this->get(route('inventory.qr.download', $this->sparepart->id));
        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // QR CODE PRINT (halaman HTML untuk cetak label)
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mengakses_halaman_print_qr_jika_ada_qr_code()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.print', $this->sparepartWithQr->id));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_print_qr_jika_ada_qr_code()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('inventory.qr.print', $this->sparepartWithQr->id));

        $response->assertStatus(200);
    }

    #[Test]
    public function operator_dapat_mengakses_halaman_print_qr_jika_ada_qr_code()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.qr.print', $this->sparepartWithQr->id));

        $response->assertStatus(200);
    }

    #[Test]
    public function print_qr_mengembalikan_404_jika_tidak_ada_qr_code()
    {
        // Sparepart tanpa qr_code_path → harus 404
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.qr.print', $this->sparepart->id));

        $response->assertStatus(404);
    }

    #[Test]
    public function guest_tidak_dapat_mengakses_halaman_print_qr()
    {
        $response = $this->get(route('inventory.qr.print', $this->sparepartWithQr->id));
        $response->assertRedirect(route('login'));
    }

    // =========================================================================
    // CHECK PART NUMBER — AJAX endpoint (JSON)
    // =========================================================================

    #[Test]
    public function check_part_number_mengembalikan_exists_true_jika_sudah_ada()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.check-part-number', [
                'part_number' => $this->sparepart->part_number,
            ]));

        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
        $response->assertJsonStructure(['exists', 'data' => ['name', 'brand', 'category']]);
    }

    #[Test]
    public function check_part_number_mengembalikan_exists_false_jika_tidak_ada()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.check-part-number', [
                'part_number' => 'PN-TIDAK-ADA-99999',
            ]));

        $response->assertStatus(200);
        $response->assertJson(['exists' => false]);
    }

    #[Test]
    public function check_part_number_dapat_diakses_oleh_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('inventory.check-part-number', [
                'part_number' => $this->sparepart->part_number,
            ]));

        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }

    #[Test]
    public function check_part_number_dapat_diakses_oleh_operator()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.check-part-number', [
                'part_number' => $this->sparepart->part_number,
            ]));

        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }

    #[Test]
    public function guest_tidak_dapat_mengakses_check_part_number()
    {
        $response = $this->get(route('inventory.check-part-number', [
            'part_number' => 'PN-TEST',
        ]));

        $response->assertRedirect(route('login'));
    }
}

