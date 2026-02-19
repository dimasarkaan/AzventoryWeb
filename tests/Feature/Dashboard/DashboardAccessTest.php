<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function superadmin_can_access_dashboard()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($user)
                         ->get(route('dashboard.superadmin'));

        $response->assertStatus(200);
        // $response->assertSee('Dashboard'); // Verify some text
        
        // Cleanup if not using RefreshDatabase/DatabaseTransactions
        $user->delete();
    }
}
