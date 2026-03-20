<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PageDetailTest — cover halaman detail/edit yang belum ter-cover sebelumnya:
 * - inventory.edit (form edit barang)
 * - users.show & users.edit (detail & edit user)
 * - inventory.borrow.show & borrow.history (detail peminjaman)
 * - global-search (pencarian global)
 * - AJAX endpoints yang return JSON (movement-data, check-part-number)
 */
class TesDetailHalaman extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $admin;
    protected User $operator;
    protected Sparepart $sparepart;
    protected Borrowing $borrowing;
    protected User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role'               => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);
        $this->admin = User::factory()->create([
            'role'               => UserRole::ADMIN,
            'password_changed_at' => now(),
        ]);
        $this->operator = User::factory()->create([
            'role'               => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        // Target user untuk test users.show/edit
        $this->targetUser = User::factory()->create([
            'role'               => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        // Sparepart untuk test inventory.edit
        $this->sparepart = Sparepart::factory()->create(['stock' => 10]);

        // Borrowing untuk test borrow.show dan borrow.history
        $this->borrowing = Borrowing::create([
            'sparepart_id'       => $this->sparepart->id,
            'user_id'            => $this->operator->id,
            'borrower_name'      => $this->operator->name,
            'quantity'           => 1,
            'borrowed_at'        => now(),
            'expected_return_at' => now()->addDays(7),
            'status'             => 'borrowed',
        ]);
    }

    // =========================================================================
    // INVENTORY EDIT — Form edit barang
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mengakses_form_edit_inventaris()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.edit', $this->sparepart->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_form_edit_inventaris()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('inventory.edit', $this->sparepart->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_form_edit_inventaris()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.edit', $this->sparepart->id));
        $response->assertStatus(403);
    }

    // =========================================================================
    // USERS SHOW & EDIT — Detail dan form edit user (superadmin only)
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mengakses_detail_user()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('users.show', $this->targetUser->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_form_edit_user()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('users.edit', $this->targetUser->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_tidak_dapat_mengakses_detail_user()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('users.show', $this->targetUser->id));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_mengakses_form_edit_user()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('users.edit', $this->targetUser->id));
        $response->assertStatus(403);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_detail_user()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('users.show', $this->targetUser->id));
        $response->assertStatus(403);
    }

    // =========================================================================
    // BORROWING SHOW & HISTORY — Detail peminjaman
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mengakses_detail_peminjaman()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.borrow.show', $this->borrowing->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_detail_peminjaman()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('inventory.borrow.show', $this->borrowing->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_dapat_mengakses_detail_peminjamannya_sendiri()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.borrow.show', $this->borrowing->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function superadmin_dapat_mengakses_history_peminjaman()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('inventory.borrow.history', $this->borrowing->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_history_peminjaman()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('inventory.borrow.history', $this->borrowing->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_dapat_mengakses_history_peminjamannya_sendiri()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.borrow.history', $this->borrowing->id));
        $response->assertStatus(200);
    }

    // =========================================================================
    // GLOBAL SEARCH — Endpoint pencarian semua role
    // =========================================================================

    #[Test]
    public function superadmin_dapat_menggunakan_global_search()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('global-search', ['query' => 'test']));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_menggunakan_global_search()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('global-search', ['query' => 'test']));
        $response->assertStatus(200);
    }

    #[Test]
    public function operator_dapat_menggunakan_global_search()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('global-search', ['query' => 'test']));
        $response->assertStatus(200);
    }

    #[Test]
    public function global_search_mengembalikan_json()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('global-search', ['query' => $this->sparepart->name]));
        $response->assertStatus(200);
        $response->assertJsonStructure(['menus', 'spareparts', 'users']);
    }

    // =========================================================================
    // AJAX ENDPOINTS — Dashboard movement-data (JSON)
    // =========================================================================

    #[Test]
    public function superadmin_dapat_mengakses_movement_data_endpoint()
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('dashboard.movement-data'));
        $response->assertStatus(200);
        $response->assertJson([]); // memastikan response valid JSON
    }

    #[Test]
    public function admin_dapat_mengakses_admin_movement_data_endpoint()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard.admin.movement-data'));
        $response->assertStatus(200);
        $response->assertJson([]);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_superadmin_movement_data()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('dashboard.movement-data'));
        $response->assertStatus(403);
    }

    #[Test]
    public function guest_tidak_dapat_mengakses_detail_inventaris()
    {
        $response = $this->get(route('inventory.edit', $this->sparepart->id));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_tidak_dapat_mengakses_detail_user()
    {
        $response = $this->get(route('users.show', $this->targetUser->id));
        $response->assertRedirect(route('login'));
    }
}

