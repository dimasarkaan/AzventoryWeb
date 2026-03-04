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
 * Test tambahan untuk ProfileController dan halaman-halaman terkait profil
 * yang belum dicakup oleh ProfileTest.php yang sudah ada.
 *
 * Mencakup: myInventory, updateSettings, dan halaman scan-qr.
 */
class ProfileExtendedTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    }

    // ── My Inventory (Riwayat Pinjaman Sendiri) ──────────────────

    #[Test]
    public function halaman_my_inventory_dapat_diakses_oleh_user_yang_login()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('profile.inventory'));

        $response->assertOk();
    }

    #[Test]
    public function halaman_my_inventory_tidak_dapat_diakses_tanpa_login()
    {
        $response = $this->get(route('profile.inventory'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function halaman_my_inventory_menampilkan_peminjaman_aktif_milik_user()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);

        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'borrower_name' => $this->operator->name,
            'quantity' => 2,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->operator)
            ->get(route('profile.inventory'));

        $response->assertOk()
            ->assertViewHas('activeBorrowings');

        $activeBorrowings = $response->viewData('activeBorrowings');
        $this->assertCount(1, $activeBorrowings);
    }

    #[Test]
    public function halaman_my_inventory_tidak_menampilkan_peminjaman_milik_user_lain()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $other = User::factory()->create(['role' => UserRole::OPERATOR]);

        // Peminjaman milik user lain
        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $other->id,
            'borrower_name' => $other->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->operator)
            ->get(route('profile.inventory'));

        $activeBorrowings = $response->viewData('activeBorrowings');
        $this->assertCount(0, $activeBorrowings);
    }

    // ── Update Settings (JSON Preferences) ───────────────────────

    #[Test]
    public function user_dapat_memperbarui_settings_profil()
    {
        $response = $this->actingAs($this->operator)
            ->patchJson(route('profile.settings.update'), [
                'settings' => ['notification_sound' => true],
            ]);

        $response->assertOk()
            ->assertJsonFragment(['status' => 'success']);

        $this->assertEquals(
            true,
            $this->operator->fresh()->settings['notification_sound']
        );
    }

    #[Test]
    public function update_settings_menggabungkan_dengan_settings_yang_sudah_ada()
    {
        // Set initial settings
        $this->operator->update(['settings' => ['lang' => 'id']]);

        $this->actingAs($this->operator)
            ->patchJson(route('profile.settings.update'), [
                'settings' => ['notification_sound' => false],
            ]);

        $fresh = $this->operator->fresh()->settings;
        $this->assertEquals('id', $fresh['lang']);
        $this->assertFalse($fresh['notification_sound']);
    }

    #[Test]
    public function update_settings_gagal_jika_settings_bukan_array()
    {
        $response = $this->actingAs($this->operator)
            ->patchJson(route('profile.settings.update'), [
                'settings' => 'bukan_array',
            ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function update_settings_tidak_dapat_diakses_tanpa_login()
    {
        $response = $this->patchJson(route('profile.settings.update'), [
            'settings' => ['key' => 'value'],
        ]);

        $response->assertUnauthorized();
    }

    // ── Scan QR Page ─────────────────────────────────────────────

    #[Test]
    public function halaman_scan_qr_dapat_diakses_oleh_user_yang_login()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.scan-qr'));

        $response->assertOk();
    }

    #[Test]
    public function halaman_scan_qr_tidak_dapat_diakses_tanpa_login()
    {
        $response = $this->get(route('inventory.scan-qr'));
        $response->assertRedirect(route('login'));
    }
}
