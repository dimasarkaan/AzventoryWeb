<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesTokenAbilitiesTest extends TestCase
{
    use RefreshDatabase;

    private function superadmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(),
        ]);
    }

    #[Test]
    public function api_token_tanpa_ability_ditolak()
    {
        $user = $this->superadmin();
        // Buat token tanpa abilities (array kosong)
        $token = $user->createToken('No Abilities', [])->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/inventory')
            ->assertStatus(403)
            ->assertJsonPath('message', "Akses Ditolak: Token API ini tidak memiliki izin 'inventory:read'.");
    }

    #[Test]
    public function api_token_dengan_read_ability_diterima_untuk_get_tapi_ditolak_untuk_post()
    {
        $user = $this->superadmin();
        // Buat token HANYA dengan ability inventory:read
        $token = $user->createToken('Read Only', ['inventory:read'])->plainTextToken;

        // BACA (GET) - HARUS BERHASIL
        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/inventory')
            ->assertStatus(200);

        // BUAT (POST) - HARUS GAGAL
        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/inventory', [
            'name' => 'Barang Baru',
        ])
            ->assertStatus(403)
            ->assertJsonPath('message', "Akses Ditolak: Token API ini tidak memiliki izin 'inventory:create'.");
    }

    #[Test]
    public function update_ability_berfungsi_untuk_put_dan_patch()
    {
        $user = $this->superadmin();
        $token = $user->createToken('Update Only', ['inventory:update'])->plainTextToken;
        $sparepart = \App\Models\Sparepart::factory()->create();

        // UPDATE (PUT) - BERHASIL
        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->putJson("/api/v1/inventory/{$sparepart->uuid}", [
            'name' => 'Diperbarui',
        ])->assertStatus(200);
    }

    #[Test]
    public function backend_api_token_store_menyimpan_abilities_dengan_benar()
    {
        $user = $this->superadmin();

        $response = $this->actingAs($user)
            ->post(route('profile.api-tokens.store'), [
                'token_name' => 'Token Testing UI',
                'abilities' => ['inventory:read', 'category:read', 'borrowing:create'],
            ]);

        $response->assertRedirect();

        $tokenModel = $user->tokens()->first();
        $this->assertNotNull($tokenModel);

        $this->assertTrue($tokenModel->can('inventory:read'));
        $this->assertTrue($tokenModel->can('category:read'));
        $this->assertTrue($tokenModel->can('borrowing:create'));

        // Assert tidak punya ability yang tidak dipilih
        $this->assertFalse($tokenModel->can('inventory:create'));
        $this->assertFalse($tokenModel->can('category:create'));
    }

    #[Test]
    public function edge_case_borrowing_return_adalah_update_bukan_create()
    {
        $user = $this->superadmin();
        // Token HANYA dengan ability borrowing:update
        $token = $user->createToken('Borrowing Updater', ['borrowing:update'])->plainTextToken;

        $sparepart = \App\Models\Sparepart::factory()->create(['stock' => 10]);
        $borrowing = \App\Models\Borrowing::factory()->create([
            'status' => 'borrowed',
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'quantity' => 1,
        ]);

        // POST ke /return. Meski HTTP Method POST, Middleware kita memetakannya ke 'update'
        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->postJson("/api/v1/borrowings/{$borrowing->id}/return", [
            'quantity' => 1,
            'condition' => 'Baik',
        ])->assertStatus(200);
    }
}
