<?php

namespace Tests\Feature\Api;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiSistemTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'superadmin'): User
    {
        return User::factory()->create(['role' => $role, 'password_changed_at' => now()]);
    }

    #[Test]
    public function stats_mengembalikan_data_ringkasan_sistem()
    {
        Sanctum::actingAs($this->makeUser());
        Sparepart::factory()->count(5)->create();

        $this->getJson('/api/v1/stats')
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'inventory' => ['total_items', 'total_stock', 'low_stock_count'],
                    'borrowing' => ['active_count', 'overdue_count'],
                    'master_data' => ['brands_count', 'categories_count', 'locations_count'],
                ],
            ]);
    }

    #[Test]
    public function me_mengembalikan_profil_user_login()
    {
        $user = $this->makeUser('operator');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/me')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => $user->email,
                    'role' => 'operator',
                ],
            ]);
    }

    #[Test]
    public function activity_logs_hanya_bisa_diakses_superadmin()
    {
        // Fail as operator
        Sanctum::actingAs($this->makeUser('operator'));
        $this->getJson('/api/v1/activity-logs')->assertStatus(403);

        // Success as superadmin
        Sanctum::actingAs($this->makeUser('superadmin'));
        $this->getJson('/api/v1/activity-logs')->assertStatus(200);
    }

    #[Test]
    public function notifications_berhasil_diakses()
    {
        Sanctum::actingAs($this->makeUser());
        $this->getJson('/api/v1/notifications')->assertStatus(200);
    }
}
