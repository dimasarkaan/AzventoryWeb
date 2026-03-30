<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiManajemenUserTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'superadmin'): User
    {
        return User::factory()->create(['role' => $role, 'password_changed_at' => now()]);
    }

    #[Test]
    public function superadmin_dapat_melihat_daftar_user()
    {
        Sanctum::actingAs($this->makeUser('superadmin'));
        User::factory()->count(2)->create();

        $this->getJson('/api/v1/users')
            ->assertStatus(200);
    }

    #[Test]
    public function operator_tidak_dapat_melihat_daftar_user()
    {
        Sanctum::actingAs($this->makeUser('operator'));
        $this->getJson('/api/v1/users')->assertStatus(403);
    }

    #[Test]
    public function superadmin_dapat_membuat_user_baru()
    {
        Sanctum::actingAs($this->makeUser('superadmin'));

        $payload = [
            'name' => 'Operator Baru',
            'email' => 'opbaru@example.com',
            'role' => 'operator',
            'status' => 'Aktif',
        ];

        $this->postJson('/api/v1/users', $payload)
            ->assertStatus(201)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('users', ['email' => 'opbaru@example.com']);
    }

    #[Test]
    public function superadmin_dapat_reset_password_user()
    {
        Sanctum::actingAs($this->makeUser('superadmin'));
        $targetUser = User::factory()->create(['password' => bcrypt('lama123')]);

        $this->postJson("/api/v1/users/{$targetUser->id}/reset-password")
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }

    #[Test]
    public function superadmin_dapat_menghapus_user_soft_delete()
    {
        Sanctum::actingAs($this->makeUser('superadmin'));
        $targetUser = User::factory()->create();

        $this->deleteJson("/api/v1/users/{$targetUser->id}")
            ->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $targetUser->id]);
    }
}
