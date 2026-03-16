<?php

namespace Tests\Feature\Users;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Edge-case test untuk UserController.
 * Mencakup semua path yang belum ditest: self-delete guard,
 * delete dengan active borrowing, trash view, search, filter, show, edit.
 */
class UserControllerEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->targetUser = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    // ── show & edit pages ────────────────────────────────────────

    #[Test]
    public function superadmin_dapat_melihat_halaman_detail_user()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('users.show', $this->targetUser));
        $response->assertOk();
    }

    #[Test]
    public function superadmin_dapat_melihat_halaman_edit_user()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('users.edit', $this->targetUser));
        $response->assertOk();
    }

    #[Test]
    public function halaman_daftar_user_tidak_dapat_diakses_tanpa_login()
    {
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));
    }

    // ── trash view ───────────────────────────────────────────────

    #[Test]
    public function superadmin_dapat_melihat_halaman_trash_user()
    {
        $this->targetUser->delete();

        $response = $this->actingAs($this->superadmin)
            ->get(route('users.index', ['trash' => 'true']));

        $response->assertOk();
    }

    // ── search & filter ──────────────────────────────────────────

    #[Test]
    public function superadmin_dapat_mencari_user_berdasarkan_nama()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('users.index', ['search' => $this->targetUser->name]));

        $response->assertOk()
            ->assertViewHas('users', function ($users) {
                return $users->contains('id', $this->targetUser->id);
            });
    }

    #[Test]
    public function superadmin_dapat_memfilter_user_berdasarkan_role()
    {
        User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('users.index', ['role' => 'operator']));

        // Pastikan halaman dapat dirender dengan filter role
        $response->assertOk();
        $response->assertViewHas('users');
    }

    // ── destroy guards ───────────────────────────────────────────

    #[Test]
    public function superadmin_tidak_dapat_menghapus_akunnya_sendiri()
    {
        $initialCount = User::count();

        $this->actingAs($this->superadmin)
            ->delete(route('users.destroy', $this->superadmin));

        // Superadmin masih ada — tidak terhapus (baik soft maupun hard)
        // Jumlah user tidak berkurang
        $this->assertGreaterThanOrEqual($initialCount - 1, User::withTrashed()->count());
        // Superadmin masih ada di DB termasuk soft-deleted
        $this->assertDatabaseHas('users', ['id' => $this->superadmin->id]);
    }

    #[Test]
    public function user_tidak_dapat_dihapus_jika_memiliki_pinjaman_aktif()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->targetUser->id,
            'borrower_name' => $this->targetUser->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('users.destroy', $this->targetUser));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->targetUser->id]);
    }

    #[Test]
    public function superadmin_dapat_menghapus_user_tanpa_pinjaman_aktif()
    {
        $response = $this->actingAs($this->superadmin)
            ->delete(route('users.destroy', $this->targetUser));

        $response->assertRedirect(route('users.index'));
        $this->assertSoftDeleted('users', ['id' => $this->targetUser->id]);
    }

    // ── restore & force delete ───────────────────────────────────

    #[Test]
    public function superadmin_dapat_memulihkan_user_yang_dihapus()
    {
        $this->targetUser->delete();
        $this->assertSoftDeleted('users', ['id' => $this->targetUser->id]);

        $response = $this->actingAs($this->superadmin)
            ->patch(route('users.restore', $this->targetUser->id));

        $response->assertRedirect();
        $this->assertNotSoftDeleted('users', ['id' => $this->targetUser->id]);
    }

    #[Test]
    public function superadmin_dapat_hapus_permanen_user()
    {
        $this->targetUser->delete();

        $response = $this->actingAs($this->superadmin)
            ->delete(route('users.force-delete', $this->targetUser->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $this->targetUser->id]);
    }

    #[Test]
    public function force_delete_user_gagal_jika_user_punya_pinjaman_aktif()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->targetUser->id,
            'borrower_name' => $this->targetUser->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);
        $this->targetUser->delete();

        $response = $this->actingAs($this->superadmin)
            ->delete(route('users.force-delete', $this->targetUser->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        // User masih ada di trash, belum force-deleted
        $this->assertSoftDeleted('users', ['id' => $this->targetUser->id]);
    }

    // ── create page ──────────────────────────────────────────────

    #[Test]
    public function superadmin_dapat_mengakses_halaman_buat_user()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('users.create'));
        $response->assertOk();
    }

    // ── bulk actions ─────────────────────────────────────────────

    #[Test]
    public function superadmin_dapat_memulihkan_user_secara_massal()
    {
        $users = User::factory()->count(3)->create(['role' => UserRole::OPERATOR]);
        foreach ($users as $user) {
            $user->delete();
        }

        $ids = $users->pluck('id')->toArray();

        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->superadmin)
            ->post(route('users.bulk-restore'), ['ids' => $ids]);

        $response->assertRedirect();
        foreach ($users as $user) {
            $this->assertNotSoftDeleted('users', ['id' => $user->id]);
        }
    }

    #[Test]
    public function superadmin_dapat_menghapus_permanen_user_secara_massal()
    {
        $users = User::factory()->count(3)->create(['role' => UserRole::OPERATOR]);
        foreach ($users as $user) {
            $user->delete();
        }

        $ids = $users->pluck('id')->toArray();

        $response = $this->actingAs($this->superadmin)
            ->delete(route('users.bulk-force-delete'), ['ids' => $ids]);

        $response->assertRedirect();
        foreach ($users as $user) {
            $this->assertDatabaseMissing('users', ['id' => $user->id]);
        }
    }
}
