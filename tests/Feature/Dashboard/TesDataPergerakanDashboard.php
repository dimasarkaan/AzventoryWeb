<?php

namespace Tests\Feature\Dashboard;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk endpoint AJAX movement-data pada dashboard Superadmin dan Admin.
 * Endpoint ini digunakan oleh tombol filter chart (7 Hari, 30 Hari, 3 Bulan).
 */
class TesDataPergerakanDashboard extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    // ── Superadmin Movement Data ─────────────────────────────────

    #[Test]
    public function superadmin_dapat_mengakses_endpoint_movement_data()
    {
        $response = $this->actingAs($this->superadmin)
            ->getJson(route('dashboard.movement-data', ['range' => 7]));

        $response->assertOk()
            ->assertJsonStructure(['labels', 'masuk', 'keluar']);
    }

    #[Test]
    public function superadmin_movement_data_default_range_tujuh_hari()
    {
        $response = $this->actingAs($this->superadmin)
            ->getJson(route('dashboard.movement-data'));

        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('labels', $data);
        $this->assertCount(7, $data['labels']);
    }

    #[Test]
    public function superadmin_movement_data_range_tiga_puluh_hari()
    {
        $response = $this->actingAs($this->superadmin)
            ->getJson(route('dashboard.movement-data', ['range' => 30]));

        $response->assertOk();
        $data = $response->json();
        // 30 hari masih daily (< 60 hari) — label = 30
        $this->assertCount(30, $data['labels']);
    }

    #[Test]
    public function superadmin_movement_data_range_tidak_bisa_melebihi_365_hari()
    {
        // Meskipun dikirim 999, server akan clamp ke 365
        $response = $this->actingAs($this->superadmin)
            ->getJson(route('dashboard.movement-data', ['range' => 999]));

        $response->assertOk();
        $data = $response->json();
        // 365 hari > 60 hari → agregasi MINGGUAN, bukan harian.
        // Label count = ~52 minggu, bukan 365 hari.
        $this->assertNotEmpty($data['labels']);
        $this->assertLessThanOrEqual(365, count($data['labels']));
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_endpoint_movement_data_superadmin()
    {
        $response = $this->actingAs($this->operator)
            ->getJson(route('dashboard.movement-data'));

        $response->assertForbidden();
    }

    // ── Admin Movement Data ──────────────────────────────────────

    #[Test]
    public function admin_dapat_mengakses_endpoint_movement_data()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('dashboard.admin.movement-data', ['range' => 7]));

        $response->assertOk()
            ->assertJsonStructure(['labels', 'masuk', 'keluar']);
    }

    #[Test]
    public function admin_movement_data_mengembalikan_data_yang_benar_berdasarkan_range()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('dashboard.admin.movement-data', ['range' => 30]));

        $response->assertOk();
        $data = $response->json();
        $this->assertCount(30, $data['labels']);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_endpoint_movement_data_admin()
    {
        $response = $this->actingAs($this->operator)
            ->getJson(route('dashboard.admin.movement-data'));

        $response->assertForbidden();
    }
}

