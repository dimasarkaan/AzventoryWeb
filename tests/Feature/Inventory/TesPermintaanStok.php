<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk StockRequestController.
 * Mencakup pengajuan perubahan stok oleh Operator (pending)
 * dan Admin/Superadmin (auto-approve).
 */
class TesPermintaanStok extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $operator;

    protected Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->sparepart = Sparepart::factory()->create(['stock' => 50, 'minimum_stock' => 5]);
    }

    #[Test]
    public function operator_dapat_mengajukan_permintaan_penambahan_stok_dengan_status_pending()
    {
        \Illuminate\Support\Facades\Notification::fake();

        $response = $this->actingAs($this->operator)
            ->post(route('inventory.stock.request.store', $this->sparepart), [
                'type' => 'masuk',
                'quantity' => 10,
                'reason' => 'Penambahan stok rutin bulanan.',
            ]);

        $response->assertRedirect(route('inventory.show', $this->sparepart));
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'type' => 'masuk',
            'quantity' => 10,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function operator_dapat_mengajukan_permintaan_pengurangan_stok_dengan_status_pending()
    {
        \Illuminate\Support\Facades\Notification::fake();

        $response = $this->actingAs($this->operator)
            ->post(route('inventory.stock.request.store', $this->sparepart), [
                'type' => 'keluar',
                'quantity' => 5,
                'reason' => 'Pengurangan stok untuk perbaikan.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'type' => 'keluar',
            'status' => 'pending',
        ]);
        // Stok TIDAK langsung berubah (butuh approval)
        $this->assertEquals(50, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function admin_mengajukan_stok_masuk_dan_langsung_disetujui()
    {
        $initialStock = $this->sparepart->stock;

        $this->actingAs($this->admin)
            ->post(route('inventory.stock.request.store', $this->sparepart), [
                'type' => 'masuk',
                'quantity' => 20,
                'reason' => 'Restock dari supplier.',
            ]);

        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'type' => 'masuk',
            'quantity' => 20,
            'status' => 'approved',
        ]);
        // Stok langsung bertambah
        $this->assertEquals($initialStock + 20, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function superadmin_mengajukan_stok_keluar_dan_stok_langsung_berkurang()
    {
        $initialStock = $this->sparepart->stock;

        $this->actingAs($this->superadmin)
            ->post(route('inventory.stock.request.store', $this->sparepart), [
                'type' => 'keluar',
                'quantity' => 10,
                'reason' => 'Dipakai untuk maintenance.',
            ]);

        $this->assertEquals($initialStock - 10, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function pengajuan_stok_keluar_gagal_jika_stok_tidak_mencukupi_untuk_admin()
    {
        $this->sparepart->update(['stock' => 3]);

        $response = $this->actingAs($this->admin)
            ->post(route('inventory.stock.request.store', $this->sparepart), [
                'type' => 'keluar',
                'quantity' => 10,
                'reason' => 'Melebihi stok tersedia.',
            ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertEquals(3, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function validasi_gagal_jika_quantity_kurang_dari_satu()
    {
        $response = $this->actingAs($this->operator)
            ->post(route('inventory.stock.request.store', $this->sparepart), [
                'type' => 'masuk',
                'quantity' => 0,
                'reason' => 'Jumlah tidak valid.',
            ]);

        $response->assertSessionHasErrors('quantity');
    }

    #[Test]
    public function validasi_gagal_jika_reason_tidak_diisi()
    {
        $response = $this->actingAs($this->operator)
            ->post(route('inventory.stock.request.store', $this->sparepart), [
                'type' => 'masuk',
                'quantity' => 5,
                'reason' => '',
            ]);

        $response->assertSessionHasErrors('reason');
    }
}

