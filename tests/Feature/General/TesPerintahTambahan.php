<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test tambahan untuk Console Commands yang belum dicakup.
 * Meliputi: app:generate-qr-codes dan api:create-token.
 */
class TesPerintahTambahan extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function perintah_generate_qr_codes_berjalan_tanpa_error()
    {
        Storage::fake('public');

        Sparepart::factory()->count(3)->create();

        $this->artisan('app:generate-qr-codes')
            ->assertExitCode(0);
    }

    #[Test]
    public function perintah_generate_qr_codes_menghasilkan_file_qr_untuk_semua_sparepart()
    {
        Storage::fake('public');

        $spareparts = Sparepart::factory()->count(2)->create();

        $this->artisan('app:generate-qr-codes')->assertExitCode(0);

        // Pastikan semua sparepart kini punya qr_code_path
        foreach ($spareparts as $sp) {
            $this->assertNotNull($sp->fresh()->qr_code_path);
        }
    }

    #[Test]
    public function perintah_generate_qr_codes_menampilkan_info_saat_tidak_ada_sparepart()
    {
        // Database kosong
        $this->artisan('app:generate-qr-codes')
            ->expectsOutput('Tidak ada sparepart ditemukan.')
            ->assertExitCode(0);
    }

    #[Test]
    public function perintah_create_api_token_berhasil_membuat_token_untuk_user()
    {
        User::factory()->create([
            'email' => 'apiuser@test.com',
            'role' => UserRole::ADMIN,
        ]);

        $this->artisan('api:create-token', [
            'name' => 'TestToken',
            'email' => 'apiuser@test.com',
        ])->assertExitCode(0);
    }

    #[Test]
    public function perintah_create_api_token_membuat_service_user_baru_jika_email_tidak_ada()
    {
        $this->artisan('api:create-token', [
            'name' => 'ServiceToken',
        ])->assertExitCode(0);

        // User service baru harus terbuat
        $this->assertDatabaseHas('users', [
            'email' => 'api-service-servicetoken@system.local',
        ]);
    }
}
