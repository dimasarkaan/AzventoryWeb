<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk BorrowingController: show dan history.
 * Memastikan detail peminjaman dan riwayat pengembalian
 * hanya bisa diakses oleh pemilik atau admin.
 */
class BorrowingDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operator;

    protected User $otherOperator;

    protected Sparepart $sparepart;

    protected Borrowing $borrowing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->otherOperator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->sparepart = Sparepart::factory()->create(['stock' => 20]);

        $this->borrowing = Borrowing::create([
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->operator->id,
            'borrower_name' => $this->operator->name,
            'quantity' => 2,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);
    }

    // ── show ─────────────────────────────────────────────────────

    #[Test]
    public function halaman_detail_peminjaman_dapat_diakses_oleh_pemilik()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.borrow.show', $this->borrowing));

        $response->assertOk();
    }

    #[Test]
    public function halaman_detail_peminjaman_dapat_diakses_oleh_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('inventory.borrow.show', $this->borrowing));

        $response->assertOk();
    }

    #[Test]
    public function halaman_detail_peminjaman_tidak_dapat_diakses_oleh_operator_lain()
    {
        $response = $this->actingAs($this->otherOperator)
            ->get(route('inventory.borrow.show', $this->borrowing));

        // BorrowingPolicy@view — hanya owner atau admin/superadmin
        $response->assertForbidden();
    }

    // ── history (JSON AJAX) ──────────────────────────────────────

    #[Test]
    public function endpoint_history_mengembalikan_json_untuk_pemilik_peminjaman()
    {
        $response = $this->actingAs($this->operator)
            ->getJson(route('inventory.borrow.history', $this->borrowing));

        $response->assertOk()
            ->assertJsonStructure([
                'borrower',
                'borrow_date',
                'total_quantity',
                'status',
                'items',
            ]);
    }

    #[Test]
    public function endpoint_history_mengembalikan_status_dan_jumlah_yang_benar()
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('inventory.borrow.history', $this->borrowing));

        $response->assertOk()
            ->assertJsonFragment([
                'total_quantity' => 2,
                'status' => 'borrowed',
            ]);
    }

    #[Test]
    public function endpoint_history_tidak_dapat_diakses_oleh_operator_lain()
    {
        $response = $this->actingAs($this->otherOperator)
            ->getJson(route('inventory.borrow.history', $this->borrowing));

        $response->assertForbidden();
    }

    #[Test]
    public function endpoint_history_mengembalikan_items_kosong_jika_belum_ada_pengembalian()
    {
        $response = $this->actingAs($this->operator)
            ->getJson(route('inventory.borrow.history', $this->borrowing));

        $response->assertOk()
            ->assertJsonFragment(['items' => []]);
    }
}
