<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesPaksaGantiPassword extends TestCase
{
    use RefreshDatabase;

    public function test_user_baru_diarahkan_ke_halaman_ganti_password()
    {
        $user = User::factory()->create([
            'password_changed_at' => null, // Simulate new user
            'role' => 'operator',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.operator'));

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHas('warning');
    }

    public function test_user_dapat_mengakses_halaman_ganti_password_tanpa_redirect_loop()
    {
        $user = User::factory()->create([
            'password_changed_at' => null,
        ]);

        $response = $this->actingAs($user)->get(route('password.change'));

        $response->assertStatus(200);
    }

    public function test_user_dengan_password_yang_sudah_diganti_dapat_mengakses_dashboard()
    {
        $user = User::factory()->create([
            'password_changed_at' => now(), // Simulate existing user
            'role' => 'operator',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.operator'));

        $response->assertStatus(200);
    }
}

