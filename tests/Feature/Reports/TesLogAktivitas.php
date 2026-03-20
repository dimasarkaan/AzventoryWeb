<?php

namespace Tests\Feature\Reports;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesLogAktivitas extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);
    }

    #[Test]
    public function dapat_memfilter_log_berdasarkan_aksi_dan_user()
    {
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);

        ActivityLog::factory()->create([
            'user_id' => $user1->id,
            'action' => 'Login',
            'description' => 'User Alice logged in',
        ]);

        ActivityLog::factory()->create([
            'user_id' => $user2->id,
            'action' => 'Update Profile',
            'description' => 'User Bob updated profile',
        ]);

        // Filter berdasarkan User Alice
        $response = $this->actingAs($this->superAdmin)->get(route('reports.activity-logs.index', [
            'user_id' => $user1->id,
        ]));

        $response->assertViewHas('activityLogs', function ($logs) use ($user1) {
            return $logs->count() === 1 && $logs->first()->user_id === $user1->id;
        });

        // Filter berdasarkan Action 'Update Profile'
        $response2 = $this->actingAs($this->superAdmin)->get(route('reports.activity-logs.index', [
            'action' => 'Update Profile',
        ]));

        $response2->assertViewHas('activityLogs', function ($logs) {
            return $logs->count() === 1 && $logs->first()->description === 'User Bob updated profile';
        });
    }

    #[Test]
    public function dapat_mengekspor_log_ke_excel_secara_langsung()
    {
        ActivityLog::factory()->count(5)->create();

        $response = $this->actingAs($this->superAdmin)->get(route('reports.activity-logs.export', [
            'format' => 'excel',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    #[Test]
    public function ekspor_pdf_mendorong_job_ke_antrean()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)->get(route('reports.activity-logs.export', [
            'format' => 'pdf',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Queue::assertPushed(\App\Jobs\ExportActivityLogJob::class);
    }
}

