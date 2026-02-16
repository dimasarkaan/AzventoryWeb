<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_is_redirected_to_change_password_page()
    {
        $user = User::factory()->create([
            'password_changed_at' => null, // Simulate new user
            'role' => 'operator',
        ]);

        $response = $this->actingAs($user)->get(route('operator.dashboard'));

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHas('warning');
    }

    public function test_user_can_access_change_password_page_without_redirect_loop()
    {
        $user = User::factory()->create([
            'password_changed_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('password.change'));

        $response->assertStatus(200);
    }

    public function test_user_with_changed_password_can_access_dashboard()
    {
        $user = User::factory()->create([
            'password_changed_at' => now(), // Simulate existing user
            'role' => 'operator',
        ]);

        $response = $this->actingAs($user)->get(route('operator.dashboard'));

        $response->assertStatus(200);
    }
}
