<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
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

    /** @test */
    public function operator_cannot_access_superadmin_dashboard()
    {
        $response = $this->actingAs($this->operator)->get(route('superadmin.dashboard'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_superadmin_dashboard()
    {
        // Assuming 'admin' role also shouldn't access superadmin dashboard if strict separation exists
        // Or if admin is allowed, we should update this test. 
        // Based on web.php middleware 'role:superadmin', admin should be 403.
        $response = $this->actingAs($this->admin)->get(route('superadmin.dashboard'));
        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login()
    {
        $response = $this->get(route('superadmin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function operator_cannot_perform_dangerous_actions()
    {
        $response = $this->actingAs($this->operator)->delete(route('superadmin.users.destroy', $this->superAdmin->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_view_audit_logs()
    {
        $response = $this->actingAs($this->operator)->get(route('superadmin.activity-logs.index'));
        $response->assertStatus(403);
    }
}
