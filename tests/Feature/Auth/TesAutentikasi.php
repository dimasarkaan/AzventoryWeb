<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesAutentikasi extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_tampil()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_dapat_otentikasi_menggunakan_halaman_login()
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'password' => 'password', // Eksplisit
            'password_changed_at' => now(),
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        // Rute dashboard mengarahkan superadmin ke superadmin.dashboard
        $response->assertRedirect(route('dashboard.superadmin'));
    }

    public function test_user_tidak_dapat_otentikasi_dengan_password_salah()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'login' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
