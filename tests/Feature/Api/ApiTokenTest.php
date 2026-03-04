<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk alur Sanctum Token secara end-to-end:
 *
 *  1. Superadmin membuat token via web route → token tersimpan di DB
 *  2. Token hasil create dapat dipakai untuk auth di API
 *  3. Token yang dicabut tidak bisa dipakai lagi
 *  4. Role selain superadmin tidak bisa buat/cabut token
 *  5. Token salah / expired → 401
 */
class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    private function superadmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(),
        ]);
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'password_changed_at' => now(),
        ]);
    }

    // =========================================================================
    // SEKSI 1 — CREATE TOKEN (Web Route: POST /profile/api-tokens)
    // =========================================================================

    #[Test]
    public function superadmin_dapat_membuat_api_token_baru()
    {
        $user = $this->superadmin();

        $response = $this->actingAs($user)
            ->post(route('profile.api-tokens.store'), [
                'token_name' => 'Token Integrasi Eksternal',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('new_api_token');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'Token Integrasi Eksternal',
        ]);
    }

    #[Test]
    public function superadmin_dapat_membuat_banyak_token_dengan_nama_berbeda()
    {
        $user = $this->superadmin();

        $this->actingAs($user)->post(route('profile.api-tokens.store'), ['token_name' => 'Token A']);
        $this->actingAs($user)->post(route('profile.api-tokens.store'), ['token_name' => 'Token B']);

        $this->assertEquals(2, $user->tokens()->count());
    }

    #[Test]
    public function pembuatan_token_gagal_jika_nama_token_kosong()
    {
        $user = $this->superadmin();

        $this->actingAs($user)
            ->post(route('profile.api-tokens.store'), ['token_name' => ''])
            ->assertSessionHasErrors('token_name');
    }

    #[Test]
    public function admin_tidak_bisa_membuat_api_token()
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)
            ->post(route('profile.api-tokens.store'), ['token_name' => 'Coba Token'])
            ->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_bisa_membuat_api_token()
    {
        $operator = $this->makeUser('operator');

        $this->actingAs($operator)
            ->post(route('profile.api-tokens.store'), ['token_name' => 'Coba Token'])
            ->assertStatus(403);
    }

    #[Test]
    public function guest_tidak_bisa_membuat_api_token()
    {
        $this->post(route('profile.api-tokens.store'), ['token_name' => 'Coba Token'])
            ->assertRedirect();
    }

    // =========================================================================
    // SEKSI 2 — ALUR TOKEN NYATA (Buat token → gunakan sebagai Bearer)
    // =========================================================================

    #[Test]
    public function token_yang_dibuat_superadmin_bisa_dipakai_untuk_api()
    {
        $user = $this->superadmin();
        $token = $user->createToken('Test Token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/inventory')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
    }

    #[Test]
    public function token_yang_dibuat_superadmin_bisa_untuk_adjust_stock()
    {
        $user = $this->superadmin();
        $token = $user->createToken('Test Token')->plainTextToken;
        $sparepart = \App\Models\Sparepart::factory()->create(['stock' => 10]);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'decrement',
            'quantity' => 3,
            'description' => 'Test via Bearer Token',
        ])->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => ['current_stock' => 7]]);

        // Pastikan user_id tersimpan dengan benar (bukan null)
        $this->assertDatabaseHas('stock_logs', [
            'user_id' => $user->id,
            'quantity' => 3,
            'type' => 'keluar',
        ]);
    }

    #[Test]
    public function token_salah_ditolak_dengan_401()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer token_palsu_tidak_valid_12345',
            'Accept' => 'application/json',
        ])->getJson('/api/v1/inventory')
            ->assertStatus(401);
    }

    #[Test]
    public function request_tanpa_header_otorisasi_ditolak_401()
    {
        $this->withHeaders(['Accept' => 'application/json'])
            ->getJson('/api/v1/inventory')
            ->assertStatus(401);
    }

    #[Test]
    public function token_format_bearer_harus_benar()
    {
        $user = $this->superadmin();
        $token = $user->createToken('Test Token')->plainTextToken;

        $this->withHeaders([
            'Authorization' => $token, // Tidak ada "Bearer " prefix
            'Accept' => 'application/json',
        ])->getJson('/api/v1/inventory')
            ->assertStatus(401);
    }

    // =========================================================================
    // SEKSI 3 — PENCABUTAN TOKEN (DELETE /profile/api-tokens/{tokenId})
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mencabut_token_miliknya()
    {
        $user = $this->superadmin();
        $token = $user->createToken('Token yang Akan Dicabut');
        $tokenId = $token->accessToken->id;

        $this->actingAs($user)
            ->delete(route('profile.api-tokens.destroy', $tokenId))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
        $this->assertEquals(0, $user->fresh()->tokens()->count());
    }

    #[Test]
    public function token_yang_dicabut_tidak_bisa_dipakai_untuk_api()
    {
        $user = $this->superadmin();
        $token = $user->createToken('Token yang Akan Dicabut');
        $tokenId = $token->accessToken->id;
        $plainToken = $token->plainTextToken;

        // Cabut token langsung via Eloquent (tanpa actingAs agar tidak ada auth state)
        $user->tokens()->where('id', $tokenId)->delete();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);

        // Reset auth guard agar tidak ada caching
        $this->app['auth']->forgetGuards();

        $this->withHeaders([
            'Authorization' => 'Bearer '.$plainToken,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/inventory')
            ->assertStatus(401);
    }

    #[Test]
    public function superadmin_tidak_bisa_mencabut_token_milik_user_lain()
    {
        $superadminA = $this->superadmin();
        $superadminB = $this->superadmin();
        $tokenB = $superadminB->createToken('Token Milik B');

        $this->actingAs($superadminA)
            ->delete(route('profile.api-tokens.destroy', $tokenB->accessToken->id))
            ->assertRedirect();

        // Token B masih ada (controller hanya hapus token milik sendiri)
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenB->accessToken->id,
            'tokenable_id' => $superadminB->id,
        ]);
    }

    #[Test]
    public function admin_tidak_bisa_mencabut_token()
    {
        $superadmin = $this->superadmin();
        $admin = $this->makeUser('admin');
        $token = $superadmin->createToken('Token Superadmin');

        $this->actingAs($admin)
            ->delete(route('profile.api-tokens.destroy', $token->accessToken->id))
            ->assertStatus(403);
    }

    // =========================================================================
    // SEKSI 4 — TOKEN JUMLAH & INFORMASI
    // =========================================================================

    #[Test]
    public function user_baru_tidak_punya_token()
    {
        $this->assertEquals(0, $this->superadmin()->tokens()->count());
    }

    #[Test]
    public function setelah_buat_token_jumlah_token_bertambah()
    {
        $user = $this->superadmin();

        $this->assertEquals(0, $user->tokens()->count());
        $this->actingAs($user)->post(route('profile.api-tokens.store'), ['token_name' => 'T1']);
        $this->assertEquals(1, $user->fresh()->tokens()->count());
        $this->actingAs($user)->post(route('profile.api-tokens.store'), ['token_name' => 'T2']);
        $this->assertEquals(2, $user->fresh()->tokens()->count());
    }

    #[Test]
    public function setelah_cabut_token_jumlah_token_berkurang()
    {
        $user = $this->superadmin();
        $token1 = $user->createToken('Token 1');
        $user->createToken('Token 2');

        $this->assertEquals(2, $user->tokens()->count());

        $this->actingAs($user)
            ->delete(route('profile.api-tokens.destroy', $token1->accessToken->id));

        $this->assertEquals(1, $user->fresh()->tokens()->count());
    }
}
