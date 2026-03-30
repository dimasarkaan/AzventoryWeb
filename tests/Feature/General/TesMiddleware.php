<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Grey-box test untuk middleware RoleMiddleware dan EnsurePasswordIsChanged.
 * Memverifikasi behavior middleware terhadap berbagai kondisi user.
 */
class TesMiddleware extends TestCase
{
    use RefreshDatabase;

    // ── RoleMiddleware ───────────────────────────────────────────

    #[Test]
    public function role_middleware_menolak_akses_jika_role_tidak_sesuai()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);

        // Rute superadmin seharusnya reject operator
        $response = $this->actingAs($operator)
            ->get(route('dashboard.superadmin'));

        $response->assertForbidden();
    }

    #[Test]
    public function role_middleware_mengizinkan_akses_jika_role_sesuai()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

        $response = $this->actingAs($superadmin)
            ->get(route('dashboard.superadmin'));

        $response->assertOk();
    }

    #[Test]
    public function role_middleware_logout_dan_redirect_jika_status_nonaktif()
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
            'status' => 'nonaktif',
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.admin'));

        // User harus di-logout dan di-redirect ke login
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    #[Test]
    public function role_middleware_admin_dapat_mengakses_inventory_tapi_tidak_manajemen_user()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Admin bisa inventory
        $this->actingAs($admin)
            ->get(route('inventory.index'))
            ->assertOk();

        // Admin tidak bisa manajemen user (hanya superadmin)
        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    #[Test]
    public function role_middleware_operator_tidak_bisa_akses_reports()
    {
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);

        // Laporan hanya untuk superadmin dan admin
        $response = $this->actingAs($operator)
            ->get(route('reports.index'));

        $response->assertForbidden();
    }

    // ── EnsurePasswordIsChanged ──────────────────────────────────

    #[Test]
    public function middleware_password_changed_redirect_user_baru_ke_change_password()
    {
        $user = User::factory()->create([
            'password_changed_at' => null,
            'role' => UserRole::OPERATOR,
        ]);

        // User baru yang belum ganti password harus diarahkan ke halaman ganti password
        $response = $this->actingAs($user)
            ->get(route('dashboard.operator'));

        $response->assertRedirect(route('password.change'));
    }

    #[Test]
    public function middleware_password_changed_mengizinkan_user_lama_melewati()
    {
        $user = User::factory()->create([
            'password_changed_at' => now()->subDays(5),
            'role' => UserRole::OPERATOR,
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard.operator'));

        $response->assertOk();
    }
}
