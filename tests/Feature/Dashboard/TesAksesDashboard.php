<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesAksesDashboard extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function superadmin_dapat_mengakses_dashboard()
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

