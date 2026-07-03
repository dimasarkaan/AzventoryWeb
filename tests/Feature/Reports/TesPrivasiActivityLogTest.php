<?php

namespace Tests\Feature\Reports;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesPrivasiActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected User $otherOperator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'name' => 'Alice Superadmin',
            'role' => \App\Enums\UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Bob Admin',
            'role' => \App\Enums\UserRole::ADMIN,
            'password_changed_at' => now(),
        ]);

        $this->operator = User::factory()->create([
            'name' => 'Charlie Operator',
            'role' => \App\Enums\UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        $this->otherOperator = User::factory()->create([
            'name' => 'Diana Operator',
            'role' => \App\Enums\UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        // Buat log aktivitas untuk masing-masing user
        ActivityLog::factory()->create(['user_id' => $this->superadmin->id, 'action' => 'Login Superadmin']);
        ActivityLog::factory()->create(['user_id' => $this->admin->id, 'action' => 'Login Admin']);
        ActivityLog::factory()->create(['user_id' => $this->operator->id, 'action' => 'Login Operator 1']);
        ActivityLog::factory()->create(['user_id' => $this->otherOperator->id, 'action' => 'Login Operator 2']);
    }

    #[Test]
    public function operator_hanya_melihat_namanya_sendiri_di_filter_user(): void
    {
        $response = $this->actingAs($this->operator)
            ->get(route('reports.activity-logs.index'));

        $response->assertStatus(200);

        // Operator harusnya cuma lihat log dia
        $response->assertSee('Login Operator 1');
        $response->assertDontSee('Login Superadmin');
        $response->assertDontSee('Login Operator 2');

        // Cek informasi leak pada filter dropdown:
        // Operator seharusnya HANYA melihat namanya sendiri di list user dropdown
        $response->assertSee('Charlie Operator');

        // Operator TIDAK BOLEH melihat user lain di HTML (mencegah information leak)
        $response->assertDontSee('Alice Superadmin');
        $response->assertDontSee('Bob Admin');
        $response->assertDontSee('Diana Operator');
    }

    #[Test]
    public function admin_tidak_melihat_superadmin_di_filter_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.activity-logs.index'));

        $response->assertStatus(200);

        // Admin dapat melihat Admin & Operator
        $response->assertSee('Bob Admin');
        $response->assertSee('Charlie Operator');

        // Tapi Admin TIDAK BOLEH melihat nama Superadmin di view atau filter dropdown
        $response->assertDontSee('Alice Superadmin');
        $response->assertDontSee('Login Superadmin');
    }

    #[Test]
    public function superadmin_melihat_semua_user_di_filter_user(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.activity-logs.index'));

        $response->assertStatus(200);

        // Superadmin melihat semuanya
        $response->assertSee('Alice Superadmin');
        $response->assertSee('Bob Admin');
        $response->assertSee('Charlie Operator');
        $response->assertSee('Diana Operator');
    }
}
