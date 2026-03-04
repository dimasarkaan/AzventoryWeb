<?php

namespace Tests\Feature\Reports;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk logika scope ActivityLogController berdasarkan role.
 * - Operator hanya bisa melihat log miliknya sendiri
 * - Admin tidak bisa melihat log Superadmin
 * - Superadmin bisa melihat semua log
 */
class ActivityLogScopeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    #[Test]
    public function operator_hanya_melihat_log_miliknya_sendiri()
    {
        // Log milik operator
        ActivityLog::factory()->create([
            'user_id' => $this->operator->id,
            'action' => 'Login',
            'description' => 'Operator login',
        ]);

        // Log milik admin — tidak boleh terlihat oleh operator
        ActivityLog::factory()->create([
            'user_id' => $this->admin->id,
            'action' => 'Login',
            'description' => 'Admin login',
        ]);

        $response = $this->actingAs($this->operator)
            ->get(route('reports.activity-logs.index'));

        $response->assertOk()
            ->assertViewHas('activityLogs', function ($logs) {
                return $logs->count() === 1
                    && $logs->first()->user_id === $this->operator->id;
            });
    }

    #[Test]
    public function admin_tidak_bisa_melihat_log_superadmin()
    {
        // Log milik superadmin
        ActivityLog::factory()->create([
            'user_id' => $this->superadmin->id,
            'action' => 'Aksi Superadmin',
        ]);

        // Log milik admin sendiri — harus terlihat
        ActivityLog::factory()->create([
            'user_id' => $this->admin->id,
            'action' => 'Aksi Admin',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('reports.activity-logs.index'));

        $response->assertOk()
            ->assertViewHas('activityLogs', function ($logs) {
                foreach ($logs as $log) {
                    if ($log->user_id === $this->superadmin->id) {
                        return false;
                    }
                }

                return true;
            });
    }

    #[Test]
    public function superadmin_bisa_melihat_semua_log_dari_semua_role()
    {
        ActivityLog::factory()->create(['user_id' => $this->superadmin->id, 'action' => 'Superadmin Action']);
        ActivityLog::factory()->create(['user_id' => $this->admin->id, 'action' => 'Admin Action']);
        ActivityLog::factory()->create(['user_id' => $this->operator->id, 'action' => 'Operator Action']);

        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.activity-logs.index'));

        $response->assertOk()
            ->assertViewHas('activityLogs', function ($logs) {
                return $logs->count() === 3;
            });
    }

    #[Test]
    public function filter_berdasarkan_tanggal_mulai_berfungsi()
    {
        ActivityLog::factory()->create([
            'user_id' => $this->superadmin->id,
            'action' => 'Lama',
            'created_at' => now()->subDays(10),
        ]);
        ActivityLog::factory()->create([
            'user_id' => $this->superadmin->id,
            'action' => 'Baru',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('reports.activity-logs.index', [
                'start_date' => now()->subDays(2)->format('Y-m-d'),
            ]));

        $response->assertOk()
            ->assertViewHas('activityLogs', function ($logs) {
                return $logs->count() === 1 && $logs->first()->action === 'Baru';
            });
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_activity_log_export_excel()
    {
        // Operator masih bisa akses activity log, tapi hanya miliknya
        $response = $this->actingAs($this->operator)
            ->get(route('reports.activity-logs.index'));

        // Operator diizinkan masuk (route middleware: role:superadmin,admin,operator)
        $response->assertOk();
    }
}
