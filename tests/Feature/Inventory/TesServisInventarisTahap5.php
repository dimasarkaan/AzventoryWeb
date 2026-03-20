<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesServisInventarisTahap5 extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $service;

    protected User $admin;

    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InventoryService::class);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->operator = User::factory()->create(['role' => UserRole::OPERATOR]);
    }

    #[Test]
    public function create_borrowing_otomatis_mencatat_stock_log_keluar()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10, 'unit' => 'Pcs']);

        $this->actingAs($this->operator);

        $data = [
            'quantity' => 2,
            'expected_return_at' => now()->addDays(3)->toDateTimeString(),
            'notes' => 'Test borrowing',
        ];

        $result = $this->service->createBorrowing($sparepart, $data);

        // 1. Cek stok berkurang
        $this->assertEquals(8, $sparepart->fresh()->stock);

        // 2. Cek record borrowing tercipta
        $this->assertDatabaseHas('borrowings', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'quantity' => 2,
        ]);

        // 3. Cek StockLog KELUAR tercipta (FITUR BARU)
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'keluar',
            'quantity' => 2,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function return_borrowing_good_condition_mencatat_stock_log_masuk()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'borrower_name' => $this->operator->name,
            'quantity' => 2,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);

        $this->actingAs($this->admin);

        $data = [
            'return_quantity' => 2,
            'return_condition' => 'good',
            'return_notes' => 'Returned in good shape',
        ];

        $this->service->returnBorrowing($borrowing, $data);

        // 1. Stok kembali bertambah
        $this->assertEquals(7, $sparepart->fresh()->stock);

        // 2. StockLog MASUK tercipta
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'type' => 'masuk',
            'quantity' => 2,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function approve_stock_request_menambah_stok_dan_update_log()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $stockLog = StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Restock',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin);

        $this->service->approveStockRequest($stockLog, 'approved');

        // 1. Stok bertambah
        $this->assertEquals(15, $sparepart->fresh()->stock);

        // 2. Status log berubah
        $this->assertEquals('approved', $stockLog->fresh()->status);
        $this->assertEquals($this->admin->id, $stockLog->fresh()->approved_by);
    }

    #[Test]
    public function bulk_approve_via_controller_bekerja_dengan_benar()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);

        $log1 = StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Bulk Restock 1',
            'status' => 'pending',
        ]);

        $log2 = StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 3,
            'reason' => 'Bulk Restock 2',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('inventory.stock-approvals.bulk-approve'), [
            'ids' => [$log1->id, $log2->id],
            'status' => 'approved',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Total stok harus 10 + 5 + 3 = 18
        $this->assertEquals(18, $sparepart->fresh()->stock);
        $this->assertEquals('approved', $log1->fresh()->status);
        $this->assertEquals('approved', $log2->fresh()->status);
    }

    #[Test]
    public function approve_stock_request_keluar_gagal_jika_stok_tidak_cukup()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 2]);
        $stockLog = StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'type' => 'keluar',
            'quantity' => 5,
            'reason' => 'Adjustment',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stok tidak mencukupi untuk permintaan ini.');

        $this->service->approveStockRequest($stockLog, 'approved');
    }
}

