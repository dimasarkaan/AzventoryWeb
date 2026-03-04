<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PageHealthTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $admin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);

        // Create some dummy data to avoid "empty" errors on some pages
        Sparepart::factory()->count(3)->create();
    }

    /**
     * Helper to test routes for a specific user.
     */
    protected function verifyRoutes($user, array $routes)
    {
        foreach ($routes as $routeName) {
            $response = $this->actingAs($user)->get(route($routeName));
            $response->assertStatus(200, "Gagal mengakses halaman: {$routeName} sebagai ".$user->role->value);
        }
    }

    #[Test]
    public function periksa_semua_halaman_utama_superadmin()
    {
        $routes = [
            'dashboard.superadmin',
            'inventory.index',
            'reports.index',
            'reports.activity-logs.index',
            'users.index',
            'profile.edit',
            'notifications.index',
        ];

        $this->verifyRoutes($this->superadmin, $routes);
    }

    #[Test]
    public function periksa_semua_halaman_utama_admin()
    {
        $routes = [
            'dashboard.admin',
            'inventory.index',
            'reports.index',
            'reports.activity-logs.index',
            'profile.edit',
        ];

        $this->verifyRoutes($this->admin, $routes);
    }

    #[Test]
    public function periksa_semua_halaman_utama_operator()
    {
        $routes = [
            'dashboard.operator',
            'inventory.index',
            'reports.activity-logs.index',
            'profile.edit',
        ];

        $this->verifyRoutes($this->operator, $routes);
    }

    #[Test]
    public function periksa_halaman_detail_inventaris()
    {
        $item = Sparepart::first();
        $response = $this->actingAs($this->superadmin)->get(route('inventory.show', $item->id));
        $response->assertStatus(200);
    }
}
