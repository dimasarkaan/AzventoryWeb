<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_tidak_dapat_melihat_daftar_user()
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $response = $this->actingAs($operator)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_admin_tidak_dapat_melihat_daftar_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_operator_tidak_dapat_membuat_user()
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $response = $this->actingAs($operator)->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'operator',
        ]);

        $response->assertForbidden();
    }

    public function test_operator_tidak_dapat_menghapus_user()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($operator)->delete(route('users.destroy', $targetUser));

        $response->assertForbidden();
    }
}
