<?php

namespace Tests\Feature\General;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesKeamanan extends TestCase
{
    use RefreshDatabase;

    protected $operator;

    protected $admin;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_dashboard_superadmin()
    {
        $response = $this->actingAs($this->operator)->get(route('dashboard.superadmin'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_mengakses_dashboard_superadmin()
    {
        // Assuming 'admin' role also shouldn't access superadmin dashboard if strict separation exists
        // Or if admin is allowed, we should update this test.
        // Based on web.php middleware 'role:superadmin', admin should be 403.
        $response = $this->actingAs($this->admin)->get(route('dashboard.superadmin'));
        $response->assertStatus(403);
    }

    #[Test]
    public function user_tanpa_autentikasi_diarahkan_ke_login()
    {
        $response = $this->get(route('dashboard.superadmin'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function operator_tidak_dapat_melakukan_tindakan_berbahaya()
    {
        $response = $this->actingAs($this->operator)->delete(route('users.destroy', $this->superAdmin->id));
        $response->assertStatus(403);
    }

    #[Test]
    public function operator_dapat_mengakses_log_aktivitas_sendiri()
    {
        $response = $this->actingAs($this->operator)->get(route('reports.activity-logs.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_manajemen_user()
    {
        $response = $this->actingAs($this->operator)->get(route('users.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_dapat_mengakses_dashboard_admin_tetapi_bukan_superadmin()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.admin'));
        $response->assertStatus(200);

        $response2 = $this->actingAs($this->admin)->get(route('dashboard.superadmin'));
        $response2->assertStatus(403);
    }

    #[Test]
    public function superadmin_dapat_mengakses_semua_dashboard()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard.superadmin'));
        $response->assertStatus(200);

        $response2 = $this->actingAs($this->superAdmin)->get(route('dashboard.admin'));
        $response2->assertStatus(200);

        $response3 = $this->actingAs($this->superAdmin)->get(route('dashboard.operator'));
        $response3->assertStatus(200);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_dashboard_admin()
    {
        $response = $this->actingAs($this->operator)->get(route('dashboard.admin'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_mengakses_manajemen_user()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(403);
    }
}

