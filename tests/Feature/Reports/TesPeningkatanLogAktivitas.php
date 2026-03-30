<?php

namespace Tests\Feature\Reports;

use App\Events\ActivityLogged;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesPeningkatanLogAktivitas extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);
    }

    #[Test]
    public function log_aktivitas_dapat_disimpan_dan_dilihat_di_halaman_index()
    {
        $log = ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'Sparepart Diperbarui',
            'description' => 'Test Deskripsi Peningkatan',
            'properties' => ['test' => ['old' => 'a', 'new' => 'b']],
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('reports.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Sparepart Diperbarui');
        $response->assertSee('Test Deskripsi Peningkatan');

        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'action' => 'Sparepart Diperbarui',
        ]);
    }

    #[Test]
    public function filter_subject_type_mengenali_keyword_baru()
    {
        ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'Stok Diupdate',
            'description' => 'Update stock',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('reports.activity-logs.index', [
            'subject_type' => 'inventory',
        ]));

        $response->assertViewHas('activityLogs', function ($logs) {
            return $logs->count() > 0 && $logs->first()->action === 'Stok Diupdate';
        });
    }

    #[Test]
    public function memancarkan_event_activity_logged_saat_log_dibuat()
    {
        Event::fake();

        $log = ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'Test Event',
            'description' => 'Testing broadcast',
        ]);

        event(new ActivityLogged($log));

        Event::assertDispatched(ActivityLogged::class);
    }
}
