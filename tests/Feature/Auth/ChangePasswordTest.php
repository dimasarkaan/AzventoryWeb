<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk ChangePasswordController.
 * Melengkapi ForcePasswordChangeTest yang sudah ada (redirect & halaman).
 * Test ini fokus pada: proses submit form, validasi, dan logika first-login.
 */
class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    // ── First Login (password_changed_at = null) ─────────────────

    #[Test]
    public function user_baru_dapat_mengganti_password_dan_mengisi_username_sekaligus()
    {
        $user = User::factory()->create([
            'password_changed_at' => null,
            'role' => UserRole::OPERATOR,
        ]);

        $response = $this->actingAs($user)
            ->post(route('password.change.store'), [
                'username' => 'username_baru_unik',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);

        $response->assertRedirect(); // redirect ke dashboard sesuai role
        $this->assertEquals('username_baru_unik', $user->fresh()->username);
        $this->assertNotNull($user->fresh()->password_changed_at);
    }

    #[Test]
    public function user_baru_gagal_ganti_password_jika_username_sudah_dipakai()
    {
        $existing = User::factory()->create(['username' => 'sudah_ada']);
        $newUser = User::factory()->create([
            'password_changed_at' => null,
            'role' => UserRole::OPERATOR,
        ]);

        $response = $this->actingAs($newUser)
            ->post(route('password.change.store'), [
                'username' => 'sudah_ada',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);

        $response->assertSessionHasErrors('username');
    }

    #[Test]
    public function user_baru_gagal_jika_password_terlalu_pendek()
    {
        $user = User::factory()->create(['password_changed_at' => null]);

        $response = $this->actingAs($user)
            ->post(route('password.change.store'), [
                'username' => 'userbaru99',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function user_baru_gagal_jika_password_confirmation_tidak_cocok()
    {
        $user = User::factory()->create(['password_changed_at' => null]);

        $response = $this->actingAs($user)
            ->post(route('password.change.store'), [
                'username' => 'userbaru99',
                'password' => 'Password123',
                'password_confirmation' => 'BerbedaPass1',
            ]);

        $response->assertSessionHasErrors('password');
    }

    // ── Existing User (password_changed_at terisi) ───────────────

    #[Test]
    public function user_lama_harus_memasukkan_password_lama_untuk_ganti_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPass123'),
            'password_changed_at' => now()->subDays(30),
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($user)
            ->post(route('password.change.store'), [
                'current_password' => 'OldPass123',
                'password' => 'NewPass456',
                'password_confirmation' => 'NewPass456',
            ]);

        $response->assertRedirect();
        // Password berhasil diubah
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPass456', $user->fresh()->password));
    }

    #[Test]
    public function user_lama_gagal_jika_password_lama_salah()
    {
        $user = User::factory()->create([
            'password' => bcrypt('OldPass123'),
            'password_changed_at' => now()->subDays(10),
        ]);

        $response = $this->actingAs($user)
            ->post(route('password.change.store'), [
                'current_password' => 'SalahPassword',
                'password' => 'NewPass456',
                'password_confirmation' => 'NewPass456',
            ]);

        $response->assertSessionHasErrors('current_password');
    }

    #[Test]
    public function setelah_ganti_password_superadmin_diarahkan_ke_dashboard_superadmin()
    {
        $superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password' => bcrypt('OldPass123'),
            'password_changed_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($superadmin)
            ->post(route('password.change.store'), [
                'current_password' => 'OldPass123',
                'password' => 'NewPass456',
                'password_confirmation' => 'NewPass456',
            ]);

        $response->assertRedirect(route('dashboard.superadmin'));
    }
}
