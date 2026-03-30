<?php

namespace Tests\Feature\Inventory;

use App\Events\InventoryUpdatedEvent;
use App\Events\StockApprovalUpdatedEvent;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesRealTime extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_mengirim_event_inventory_updated_saat_stok_disetujui()
    {
        Event::fake([InventoryUpdatedEvent::class]);

        $admin = User::factory()->create(['role' => 'admin']);
        $sparepart = Sparepart::factory()->create([
            'stock' => 10,
            'minimum_stock' => 5,
        ]);

        $this->actingAs($admin);

        // Buat stock log approve
        $log = clone $sparepart; // just for dummy
        $log = StockLog::factory()->create([
            'sparepart_id' => $sparepart->id,
            'status' => 'pending',
            'quantity' => 5,
            'type' => 'masuk',
        ]);

        app(InventoryService::class)->approveStockRequest($log, 'approved');

        // Check stock updated
        $this->assertEquals(15, $sparepart->fresh()->stock);

        Event::assertDispatched(InventoryUpdatedEvent::class, function ($event) use ($sparepart) {
            return $event->sparepart->id === $sparepart->id && $event->sparepart->stock === 15;
        });
    }

    #[Test]
    public function test_mengirim_event_stock_approval_updated_saat_pengajuan_dibuat()
    {
        Event::fake([StockApprovalUpdatedEvent::class]);

        $operator = User::factory()->create(['role' => 'operator']);
        $sparepart = Sparepart::factory()->create([
            'stock' => 10,
            'type' => 'asset',
            'minimum_stock' => 0,
        ]);

        $this->actingAs($operator);

        $response = $this->post(route('inventory.stock.request.store', $sparepart), [
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Test reason real-time',
        ]);

        $response->assertRedirect();

        Event::assertDispatched(StockApprovalUpdatedEvent::class, function ($event) {
            return $event->action === 'created' && $event->stockLog->status === 'pending';
        });
    }

    #[Test]
    public function test_mengirim_event_stock_approval_updated_saat_pengajuan_diproses()
    {
        Event::fake([StockApprovalUpdatedEvent::class]);

        $admin = User::factory()->create(['role' => 'admin']);
        $sparepart = Sparepart::factory()->create(['stock' => 10]);

        $log = StockLog::factory()->create([
            'sparepart_id' => $sparepart->id,
            'status' => 'pending',
            'quantity' => 5,
            'type' => 'masuk',
        ]);

        $this->actingAs($admin);

        $response = $this->patch(route('inventory.stock-approvals.update', $log), [
            'status' => 'approved',
        ]);

        $response->assertRedirect();

        Event::assertDispatched(StockApprovalUpdatedEvent::class, function ($event) use ($log) {
            return $event->action === 'processed' && $event->stockLog->id === $log->id;
        });
    }
}
