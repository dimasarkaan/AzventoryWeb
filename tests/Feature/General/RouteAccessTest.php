<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman publik harus bisa diakses oleh guest (pengunjung).
     */
    public function test_halaman_publik_dapat_diakses_oleh_guest()
    {
        // Halaman Login
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Halaman Forgot Password
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);

        // Halaman Utama (Sekarang menampilkan Landing Page)
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test: Halaman terproteksi harus redirect ke login jika belum login.
     */
    public function test_rute_terproteksi_mengarahkan_ke_login_untuk_user_tanpa_autentikasi()
    {
        $routes = [
            '/dashboard',
            '/inventory',
            '/inventory/create',
            '/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /**
     * Test: Superadmin bisa mengakses semua halaman vital.
     */
    public function test_superadmin_dapat_mengakses_semua_halaman_kritis()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

        $this->actingAs($superadmin);

        $pages = [
            '/dashboard/superadmin' => 200,
            '/inventory' => 200,
            '/inventory/create' => 200,
            '/users' => 200,        // Manajemen User
            '/reports' => 200,      // Laporan
            '/profile' => 200,
        ];

        foreach ($pages as $url => $status) {
            $response = $this->get($url);
            $response->assertStatus($status);
        }
    }

    /**
     * Test: Operator memiliki akses terbatas.
     */
    public function test_operator_memiliki_akses_terbatas()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);

        $this->actingAs($operator);

        // Bisa akses inventory standard
        $this->get('/inventory')->assertStatus(200);

        // Bisa akses dashboard operator
        $this->get('/dashboard/operator')->assertStatus(200);

        // TIDAK BISA akses dashboard superadmin
        $this->get('/dashboard/superadmin')->assertStatus(403); // Forbidden

        // TIDAK BISA akses manajemen user
        $this->get('/users')->assertStatus(403);

        // TIDAK BISA akses laporan
        $this->get('/reports')->assertStatus(403);
    }

    /**
     * Test: Redirect dashboard sesuai role.
     */
    public function test_dashboard_mengarahkan_sesuai_role()
    {
        // 1. Superadmin -> /dashboard/superadmin
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->actingAs($superadmin)
            ->get('/dashboard')
            ->assertRedirect(route('dashboard.superadmin'));

        // 2. Admin -> /dashboard/admin
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('dashboard.admin'));

        // 3. Operator -> /dashboard/operator
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->actingAs($operator)
            ->get('/dashboard')
            ->assertRedirect(route('dashboard.operator'));
    }
}
