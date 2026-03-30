<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PageAvailabilityTest — mengisi gap dari PageHealthTest dan RouteAccessTest yang sudah ada.
 *
 * Coverage baru:
 * - Halaman scan-qr, stock-approvals, my-inventory, change-password, users CRUD
 * - Role boundary untuk role Admin (belum ter-cover sebelumnya)
 * - Role boundary lanjutan untuk Operator pada halaman CRUD
 */
class TesKetersediaanHalaman extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);
        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'password_changed_at' => now(),
        ]);
        $this->operator = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        // Data dummy agar halaman tidak error karena kosong
        Sparepart::factory()->count(3)->create();
    }

    // =========================================================================
    // SUPERADMIN — Halaman yang belum ter-cover di PageHealthTest
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mengakses_halaman_scan_qr()
    {
        $response = $this->actingAs($this->superadmin)->get(route('inventory.scan-qr'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_buat_inventaris()
    {
        $response = $this->actingAs($this->superadmin)->get(route('inventory.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_stock_approvals()
    {
        $response = $this->actingAs($this->superadmin)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_buat_user()
    {
        $response = $this->actingAs($this->superadmin)->get(route('users.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_my_inventory()
    {
        $response = $this->actingAs($this->superadmin)->get(route('profile.inventory'));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_halaman_ganti_password()
    {
        $response = $this->actingAs($this->superadmin)->get(route('password.change'));
        $response->assertStatus(200);
    }

    // =========================================================================
    // ADMIN — Halaman yang seharusnya bisa diakses
    // =========================================================================

    #[Test]
    public function admin_dapat_mengakses_halaman_scan_qr()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.scan-qr'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_buat_inventaris()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_stock_approvals()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_my_inventory()
    {
        $response = $this->actingAs($this->admin)->get(route('profile.inventory'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_halaman_ganti_password()
    {
        $response = $this->actingAs($this->admin)->get(route('password.change'));
        $response->assertStatus(200);
    }

    // =========================================================================
    // ADMIN — Role boundary (tidak boleh akses halaman Superadmin)
    // =========================================================================

    #[Test]
    public function admin_tidak_dapat_mengakses_dashboard_superadmin()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.superadmin'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_mengakses_manajemen_user()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_membuat_user_baru()
    {
        $response = $this->actingAs($this->admin)->get(route('users.create'));
        $response->assertStatus(403);
    }

    // =========================================================================
    // OPERATOR — Halaman yang seharusnya bisa diakses
    // =========================================================================

    #[Test]
    public function operator_dapat_mengakses_halaman_scan_qr()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.scan-qr'));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_dapat_mengakses_halaman_my_inventory()
    {
        $response = $this->actingAs($this->operator)->get(route('profile.inventory'));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_dapat_mengakses_halaman_ganti_password()
    {
        $response = $this->actingAs($this->operator)->get(route('password.change'));
        $response->assertStatus(200);
    }

    // =========================================================================
    // OPERATOR — Role boundary (tidak boleh akses halaman Admin/Superadmin)
    // =========================================================================

    #[Test]
    public function operator_tidak_dapat_mengakses_dashboard_admin()
    {
        $response = $this->actingAs($this->operator)->get(route('dashboard.admin'));
        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_membuat_inventaris_baru()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.create'));
        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_stock_approvals()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_manajemen_user()
    {
        $response = $this->actingAs($this->operator)->get(route('users.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_laporan()
    {
        $response = $this->actingAs($this->operator)->get(route('reports.index'));
        $response->assertStatus(403);
    }

    // =========================================================================
    // GUEST — Semua halaman baru juga harus redirect ke login
    // =========================================================================

    #[Test]
    public function guest_tidak_dapat_mengakses_scan_qr()
    {
        $response = $this->get(route('inventory.scan-qr'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_tidak_dapat_mengakses_my_inventory()
    {
        $response = $this->get(route('profile.inventory'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_tidak_dapat_mengakses_halaman_ganti_password()
    {
        $response = $this->get(route('password.change'));
        $response->assertRedirect(route('login'));
    }
}
