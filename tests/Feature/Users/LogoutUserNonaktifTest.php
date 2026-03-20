<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutUserNonaktifTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_nonaktif_otomatis_terlogout_dari_halaman_profil()
    {
        // 1. Buat user aktif
        $user = User::factory()->create([
            'status' => 'aktif',
            'password_changed_at' => now(), // Lewati middleware ganti password
        ]);

        // 2. Login sebagai user tersebut
        $this->actingAs($user);

        // 3. Pastikan bisa akses profil
        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);

        // 4. Ubah status jadi nonaktif (simulasi aksi admin di DB)
        $user->update(['status' => 'nonaktif']);

        // 5. Akses profil lagi
        $response = $this->get(route('profile.edit'));

        // 6. Harus di-redirect ke login dengan error
        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors(['login' => 'Akun Anda telah dinonaktifkan oleh Administrator.']);
    }

    /** @test */
    public function user_nonaktif_tidak_bisa_login()
    {
        $user = User::factory()->create([
            'status' => 'nonaktif',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'login' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['login' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.']);
        $this->assertGuest();
    }
}
