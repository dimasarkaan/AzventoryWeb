<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesPermintaanStokTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $admin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'password_changed_at' => now(),
        ]);

        $this->operator = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);
    }

    public function test_vip_auto_approve_kurang_stok()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);

        $response = $this->actingAs($this->superadmin)->post(route('inventory.stock.request.store', $sparepart), [
            'type' => 'keluar',
            'quantity' => 2,
            'reason' => 'Dipakai di mesin A',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 8,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->superadmin->id,
            'status' => 'approved',
            'type' => 'keluar',
            'quantity' => 2,
        ]);
    }

    public function test_reguler_pending_tambah_stok()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);

        $response = $this->actingAs($this->operator)->post(route('inventory.stock.request.store', $sparepart), [
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Barang baru datang',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 5,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'status' => 'pending',
            'type' => 'masuk',
            'quantity' => 5,
        ]);
    }

    public function test_approval_tunggal_setuju_stok_bertambah()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $stockLog = StockLog::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 10,
            'status' => 'pending',
            'reason' => 'Tambahan',
        ]);

        $response = $this->actingAs($this->admin)->put(route('inventory.stock-approvals.update', $stockLog), [
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 15,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'id' => $stockLog->id,
            'status' => 'approved',
            'approved_by' => $this->admin->id,
        ]);
    }

    public function test_approval_massal_sekaligus()
    {
        $sparepart1 = Sparepart::factory()->create(['stock' => 5]);
        $sparepart2 = Sparepart::factory()->create(['stock' => 2]);

        $log1 = StockLog::factory()->create(['sparepart_id' => $sparepart1->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 5, 'status' => 'pending', 'reason' => 'T']);
        $log2 = StockLog::factory()->create(['sparepart_id' => $sparepart2->id, 'user_id' => $this->operator->id, 'type' => 'keluar', 'quantity' => 1, 'status' => 'pending', 'reason' => 'K']);

        $response = $this->actingAs($this->admin)->post(route('inventory.stock-approvals.bulk-approve'), [
            'ids' => [$log1->id, $log2->id],
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart1->id, 'stock' => 10]);
        $this->assertDatabaseHas('spareparts', ['id' => $sparepart2->id, 'stock' => 1]);
    }

    public function test_penolakan_request_mutasi_stok()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $stockLog = StockLog::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 10,
            'status' => 'pending',
            'reason' => 'T',
        ]);

        $response = $this->actingAs($this->admin)->put(route('inventory.stock-approvals.update', $stockLog), [
            'status' => 'rejected',
            'rejection_reason' => 'Tidak perlu',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'stock' => 5]);
        $this->assertDatabaseHas('stock_logs', ['id' => $stockLog->id, 'status' => 'rejected', 'rejection_reason' => 'Tidak perlu']);
    }

    public function test_validasi_menolak_keluar_melebihi_stok()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 3]);

        $response = $this->actingAs($this->operator)->post(route('inventory.stock.request.store', $sparepart), [
            'type' => 'keluar',
            'quantity' => 5, // Lebih dari stok 3
            'reason' => 'Minta lebih',
        ]);

        $response->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'stock' => 3]);
    }

    public function test_operator_forbidden_akses_halaman_approval()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(403);
    }

    public function test_mencegah_double_submission_approval()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $stockLog = StockLog::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 5,
            'status' => 'approved', // Sudah pernah di-approve!
            'reason' => 'T',
        ]);

        $response = $this->actingAs($this->admin)->put(route('inventory.stock-approvals.update', $stockLog), [
            'status' => 'approved',
        ]);

        // Error bisa berupa forbidden, bad request, atau redirect back with error/validation errors.
        // Kita hanya asert bahwa stok tidak bertambah menjadi 10.
        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'stock' => 5]);
    }
}
