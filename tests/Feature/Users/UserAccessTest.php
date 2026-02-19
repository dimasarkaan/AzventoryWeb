<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_cannot_view_user_list()
    {
        $operator = User::factory()->create(['role' => 'operator']);

        $response = $this->actingAs($operator)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_admin_cannot_view_user_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_operator_cannot_create_user()
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

    public function test_operator_cannot_delete_user()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($operator)->delete(route('users.destroy', $targetUser));

        $response->assertForbidden();
    }
}
