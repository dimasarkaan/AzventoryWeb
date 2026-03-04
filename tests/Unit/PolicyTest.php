<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use App\Policies\BorrowingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit test untuk BorrowingPolicy dan UserPolicy.
 * Memastikan aturan otorisasi diterapkan dengan benar.
 */
class PolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat record Borrowing langsung (tidak ada BorrowingFactory).
     */
    protected function createBorrowing(int $userId): Borrowing
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);

        return Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $userId,
            'borrower_name' => 'Test Borrower',
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);
    }

    // ── BorrowingPolicy ──────────────────────────────────────────

    #[Test]
    public function borrowing_policy_semua_role_bisa_melihat_daftar_peminjaman()
    {
        $policy = new BorrowingPolicy;
        $this->assertTrue($policy->viewAny(User::factory()->make(['role' => UserRole::SUPERADMIN])));
        $this->assertTrue($policy->viewAny(User::factory()->make(['role' => UserRole::ADMIN])));
        $this->assertTrue($policy->viewAny(User::factory()->make(['role' => UserRole::OPERATOR])));
    }

    #[Test]
    public function borrowing_policy_admin_bisa_melihat_semua_peminjaman()
    {
        $policy = new BorrowingPolicy;
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $other = User::factory()->create(['role' => UserRole::OPERATOR]);
        $borrowing = $this->createBorrowing($other->id);

        $this->assertTrue($policy->view($admin, $borrowing));
    }

    #[Test]
    public function borrowing_policy_operator_hanya_dapat_melihat_peminjamannya_sendiri()
    {
        $policy = new BorrowingPolicy;
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $other = User::factory()->create(['role' => UserRole::OPERATOR]);
        $ownBorrow = $this->createBorrowing($operator->id);
        $otherBorrow = $this->createBorrowing($other->id);

        $this->assertTrue($policy->view($operator, $ownBorrow));
        $this->assertFalse($policy->view($operator, $otherBorrow));
    }

    #[Test]
    public function borrowing_policy_hanya_superadmin_yang_bisa_hapus_permanen()
    {
        $policy = new BorrowingPolicy;
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $borrowing = $this->createBorrowing($admin->id);

        $this->assertTrue($policy->forceDelete($superadmin, $borrowing));
        $this->assertFalse($policy->forceDelete($admin, $borrowing));
    }

    #[Test]
    public function borrowing_policy_semua_user_bisa_mengajukan_peminjaman()
    {
        $policy = new BorrowingPolicy;
        $operator = User::factory()->make(['role' => UserRole::OPERATOR]);

        $this->assertTrue($policy->create($operator));
    }

    // ── UserPolicy ───────────────────────────────────────────────

    #[Test]
    public function user_policy_hanya_superadmin_yang_bisa_lihat_daftar_user()
    {
        $policy = new UserPolicy;
        $superadmin = User::factory()->make(['role' => UserRole::SUPERADMIN]);
        $admin = User::factory()->make(['role' => UserRole::ADMIN]);

        $this->assertTrue($policy->viewAny($superadmin));
        $this->assertFalse($policy->viewAny($admin));
    }

    #[Test]
    public function user_policy_superadmin_bisa_delete_orang_lain_tapi_tidak_dirinya_sendiri()
    {
        $policy = new UserPolicy;
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $other = User::factory()->create(['role' => UserRole::ADMIN]);

        // Update boleh
        $this->assertTrue($policy->update($superadmin, $other));

        // Delete dirinya sendiri dilarang
        $this->assertFalse($policy->delete($superadmin, $superadmin));

        // Delete orang lain boleh
        $this->assertTrue($policy->delete($superadmin, $other));
    }

    #[Test]
    public function user_policy_user_bisa_melihat_profilnya_sendiri_tapi_tidak_profil_orang_lain()
    {
        $policy = new UserPolicy;
        $user = User::factory()->create(['role' => UserRole::OPERATOR]);
        $other = User::factory()->create(['role' => UserRole::OPERATOR]);

        $this->assertTrue($policy->view($user, $user));   // profil sendiri
        $this->assertFalse($policy->view($user, $other)); // profil orang lain
    }

    #[Test]
    public function user_policy_admin_tidak_bisa_membuat_user_baru()
    {
        $policy = new UserPolicy;
        $admin = User::factory()->make(['role' => UserRole::ADMIN]);

        $this->assertFalse($policy->create($admin));
    }
}
