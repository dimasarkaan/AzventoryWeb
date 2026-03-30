<?php

namespace Tests\Feature\Profile;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Black-box test untuk ApiTokenController.
 * Memastikan pembuatan dan pencabutan API token berjalan
 * dengan benar dan membatasi akses hanya untuk Superadmin.
 */
class TesKontrollerTokenApi extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    }

    // ── store (buat token) ───────────────────────────────────────

    #[Test]
    public function superadmin_dapat_membuat_api_token_baru()
    {
        $this->assertEquals(0, $this->superadmin->tokens()->count());

        $response = $this->actingAs($this->superadmin)
            ->post(route('profile.api-tokens.store'), [
                'token_name' => 'Token Integrasi HR',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('new_api_token');

        $this->assertEquals(1, $this->superadmin->tokens()->count());
    }

    #[Test]
    public function admin_tidak_dapat_membuat_api_token()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('profile.api-tokens.store'), [
                'token_name' => 'Token Ilegal',
            ]);

        $response->assertForbidden();
        $this->assertEquals(0, $this->admin->tokens()->count());
    }

    #[Test]
    public function membuat_api_token_gagal_jika_nama_kosong()
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('profile.api-tokens.store'), [
                'token_name' => '',
            ]);

        $response->assertSessionHasErrors('token_name');
        $this->assertEquals(0, $this->superadmin->tokens()->count());
    }

    #[Test]
    public function membuat_api_token_gagal_tanpa_login()
    {
        $response = $this->post(route('profile.api-tokens.store'), [
            'token_name' => 'Token Tanpa Login',
        ]);

        $response->assertRedirect(route('login'));
    }

    // ── destroy (hapus token) ────────────────────────────────────

    #[Test]
    public function superadmin_dapat_mencabut_api_token_miliknya()
    {
        $token = $this->superadmin->createToken('Token Lama');
        $tokenId = $this->superadmin->tokens()->first()->id;

        $this->assertEquals(1, $this->superadmin->tokens()->count());

        $response = $this->actingAs($this->superadmin)
            ->delete(route('profile.api-tokens.destroy', $tokenId));

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertEquals(0, $this->superadmin->tokens()->count());
    }

    #[Test]
    public function superadmin_tidak_bisa_mencabut_token_milik_user_lain()
    {
        // Buat token untuk superadmin lain
        $otherSuperadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $otherSuperadmin->createToken('Token Orang Lain');
        $tokenId = $otherSuperadmin->tokens()->first()->id;

        // Coba hapus dari superadmin pertama
        $this->actingAs($this->superadmin)
            ->delete(route('profile.api-tokens.destroy', $tokenId));

        // Token orang lain harus tetap ada
        $this->assertEquals(1, $otherSuperadmin->tokens()->count());
    }

    #[Test]
    public function admin_tidak_dapat_mencabut_api_token()
    {
        $token = $this->superadmin->createToken('Token Superadmin');
        $tokenId = $this->superadmin->tokens()->first()->id;

        $response = $this->actingAs($this->admin)
            ->delete(route('profile.api-tokens.destroy', $tokenId));

        $response->assertForbidden();
        // Token superadmin tidak terhapus
        $this->assertEquals(1, $this->superadmin->tokens()->count());
    }
}
