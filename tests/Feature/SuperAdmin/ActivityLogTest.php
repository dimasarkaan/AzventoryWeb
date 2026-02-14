<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
    }

    /** @test */
    public function can_filter_logs_by_action_and_user()
    {
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);

        ActivityLog::factory()->create([
            'user_id' => $user1->id,
            'action' => 'Login',
            'description' => 'User Alice logged in'
        ]);

        ActivityLog::factory()->create([
            'user_id' => $user2->id,
            'action' => 'Update Profile',
            'description' => 'User Bob updated profile'
        ]);

        // Filter by User Alice
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.activity-logs.index', [
            'user_id' => $user1->id
        ]));
        
        $response->assertSee('User Alice logged in');
        $response->assertDontSee('User Bob updated profile');

        // Filter by Action 'Update Profile'
        $response2 = $this->actingAs($this->superAdmin)->get(route('superadmin.activity-logs.index', [
            'action' => 'Update Profile'
        ]));

        $response2->assertSee('User Bob updated profile');
        $response2->assertDontSee('User Alice logged in');
    }

    /** @test */
    public function can_export_logs_to_excel_directly()
    {
        ActivityLog::factory()->count(5)->create();

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.activity-logs.export', [
            'format' => 'excel'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel');
    }

    /** @test */
    public function export_pdf_dispatches_job()
    {
        Queue::fake();

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.activity-logs.export', [
            'format' => 'pdf'
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Queue::assertPushed(\App\Jobs\ExportActivityLogJob::class);
    }
}
