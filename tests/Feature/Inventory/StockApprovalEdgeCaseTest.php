<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk path yang belum dicakup di StockApprovalTest:
 * - Alur reject (stok tidak berubah)
 * - Operator diblokir akses halaman persetujuan
 * - Reject type keluar → stok tidak berkurang
 */
class StockApprovalEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Notification::fake();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->sparepart = Sparepart::factory()->create(['stock' => 20]);
    }

    #[Test]
    public function operator_tidak_dapat_mengakses_halaman_persetujuan_stok()
    {
        $response = $this->actingAs($this->operator)
            ->get(route('inventory.stock-approvals.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function superadmin_dapat_menolak_permintaan_stok_masuk_dan_stok_tidak_berubah()
    {
        $initialStock = $this->sparepart->stock;

        $log = StockLog::create([
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 10,
            'reason' => 'Restock operator',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status' => 'rejected',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stock_logs', [
            'id' => $log->id,
            'status' => 'rejected',
        ]);
        // Stok harus TIDAK berubah setelah rejected
        $this->assertEquals($initialStock, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function admin_dapat_menolak_permintaan_stok_keluar_dan_stok_tidak_berkurang()
    {
        $initialStock = $this->sparepart->stock;

        $log = StockLog::create([
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'keluar',
            'quantity' => 5,
            'reason' => 'Permintaan keluar',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status' => 'rejected',
            ]);

        $this->assertEquals($initialStock, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function persetujuan_stok_masuk_meningkatkan_stok_sparepart()
    {
        $initialStock = $this->sparepart->stock;

        $log = StockLog::create([
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 15,
            'reason' => 'Restock',
            'status' => 'pending',
        ]);

        $this->actingAs($this->superadmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status' => 'approved',
            ]);

        $this->assertEquals($initialStock + 15, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function persetujuan_stok_keluar_mengurangi_stok_sparepart()
    {
        $initialStock = $this->sparepart->stock;

        $log = StockLog::create([
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'keluar',
            'quantity' => 5,
            'reason' => 'Dipakai maintenance',
            'status' => 'pending',
        ]);

        $this->actingAs($this->superadmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status' => 'approved',
            ]);

        $this->assertEquals($initialStock - 5, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function persetujuan_keluar_ditolak_jika_stok_tidak_cukup()
    {
        $this->sparepart->update(['stock' => 2]);
        $initialStock = 2;

        $log = StockLog::create([
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'keluar',
            'quantity' => 10, // lebih dari stok
            'reason' => 'Keluar melebihi stok',
            'status' => 'pending',
        ]);

        $this->actingAs($this->superadmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status' => 'approved',
            ]);

        // Stok tidak boleh negatif
        $this->assertGreaterThanOrEqual(0, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function persetujuan_stok_tidak_dapat_diproses_dua_kali()
    {
        $log = StockLog::create([
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Restock',
            'status' => 'approved', // sudah diproses!
            'approved_by' => $this->superadmin->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status' => 'rejected',
            ]);

        // Harus mengembalikan error karena sudah diproses
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
