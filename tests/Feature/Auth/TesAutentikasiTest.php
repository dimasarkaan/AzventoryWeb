<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesAutentikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_tampil()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_superadmin_login_menggunakan_email_redirect_ke_dashboard_superadmin()
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'password' => 'password',
            'status' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard.superadmin'));
    }

    public function test_admin_login_menggunakan_email_redirect_ke_dashboard_admin()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password',
            'status' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard.admin'));
    }

    public function test_operator_login_menggunakan_email_redirect_ke_dashboard_operator()
    {
        $user = User::factory()->create([
            'role' => 'operator',
            'password' => 'password',
            'status' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard.operator'));
    }

    public function test_user_dapat_otentikasi_menggunakan_username()
    {
        $user = User::factory()->create([
            'username' => 'johndoe',
            'role' => 'operator',
            'password' => 'password',
            'status' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'login' => 'johndoe',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard.operator'));
    }

    public function test_berhasil_login_mencatat_activity_log()
    {
        $user = User::factory()->create([
            'role' => 'operator',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Login',
        ]);
    }

    public function test_proses_logout_berhasil_dan_mencatat_activity_log()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Logout',
        ]);
    }

    public function test_fitur_remember_me_berfungsi()
    {
        $user = User::factory()->create([
            'password' => 'password',
            'status' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
            'remember' => 'on',
        ]);

        $this->assertAuthenticatedAs($user);
        /** @var \Illuminate\Auth\SessionGuard $guard */
        $guard = auth()->guard();
        $response->assertCookie($guard->getRecallerName());
    }

    public function test_login_ditolak_jika_status_akun_nonaktif()
    {
        $user = User::factory()->create([
            'status' => 'nonaktif',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.']);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'Login Ditolak',
        ]);
    }

    public function test_login_gagal_jika_password_salah_dan_mencatat_log()
    {
        $user = User::factory()->create([
            'password' => 'password',
            'status' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'salahpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'Login Gagal',
        ]);
    }

    public function test_login_gagal_jika_input_kosong()
    {
        $response = $this->post('/login', [
            'login' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['login', 'password']);
        $this->assertGuest();
    }

    public function test_brute_force_memblokir_akun_setelah_5_kali_gagal()
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        // Percobaan gagal ke-1 s/d 5
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'login' => $user->email,
                'password' => 'salahpassword',
            ]);
            $this->assertGuest();
        }

        // Percobaan ke-6 harusnya diblokir
        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password', // Even if correct, it should be blocked
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
        $this->assertStringContainsString('detik', session('errors')->first('login'));
    }
}
