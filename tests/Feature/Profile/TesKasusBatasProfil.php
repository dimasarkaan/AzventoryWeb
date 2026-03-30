<?php

namespace Tests\Feature\Profile;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test edge case untuk ProfileController.
 * - User tidak bisa hapus akun jika punya pinjaman aktif
 * - Profil hanya bisa diakses setelah login
 * - Return item dari halaman profil
 */
class TesKasusBatasProfil extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Storage::fake('public');
        // Email HARUS lowercase agar lulus rule 'lowercase' di ProfileUpdateRequest
        // is_username_changed = true agar ProfileUpdateRequest tidak menambahkan rule username
        $this->user = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'email' => 'testoperator@example.com',
            'is_username_changed' => true,
        ]);
    }

    // ── Akses halaman profil ─────────────────────────────────────

    #[Test]
    public function profil_dapat_diakses_setelah_login()
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.edit'));
        $response->assertOk();
    }

    #[Test]
    public function profil_tidak_dapat_diakses_tanpa_login()
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    // ── Update profil ────────────────────────────────────────────

    #[Test]
    public function user_dapat_mengupdate_nama_profil()
    {
        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Nama Baru Saya',
                'email' => $this->user->email,
            ]);

        $response->assertRedirect();
        $this->assertEquals('Nama Baru Saya', $this->user->fresh()->name);
    }

    #[Test]
    public function update_profil_gagal_jika_email_sudah_dipakai_user_lain()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => $this->user->name,
                'email' => 'existing@example.com',
            ]);

        // Validation must fail
        $response->assertSessionHasErrors('email');
    }

    // ── Hapus akun dengan pinjaman aktif ─────────────────────────

    #[Test]
    public function user_tidak_bisa_hapus_akun_jika_punya_pinjaman_aktif()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->user->id,
            'borrower_name' => $this->user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);

        $this->actingAs($this->user)
            ->delete(route('profile.destroy'), ['password' => 'password']);

        // User masih ada di database
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    // ── Avatar Upload ────────────────────────────────────────────

    #[Test]
    public function user_dapat_mengupload_avatar_di_profil()
    {
        $this->mock(\App\Services\ImageOptimizationService::class, function ($mock) {
            $mock->shouldReceive('optimizeAndSave')->andReturn('avatars/test.webp');
        });

        $avatar = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $avatar,
            ]);

        $response->assertRedirect();
    }

    // ── My Inventory (Profil borrow page) ────────────────────────

    #[Test]
    public function halaman_my_inventory_hanya_menampilkan_pinjaman_milik_user_ini()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $otherUser = User::factory()->create(['role' => UserRole::OPERATOR]);

        // Pinjaman milik user ini
        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->user->id,
            'borrower_name' => $this->user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        // Pinjaman milik user lain (TIDAK boleh muncul)
        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $otherUser->id,
            'borrower_name' => $otherUser->name,
            'quantity' => 2,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('profile.inventory'));

        $response->assertOk();
        $activeBorrowings = $response->viewData('activeBorrowings');
        $this->assertCount(1, $activeBorrowings);
    }
}
