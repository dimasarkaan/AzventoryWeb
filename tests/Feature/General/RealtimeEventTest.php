<?php

namespace Tests\Feature\General;

use App\Events\InventoryUpdatedEvent;
use App\Events\StockCriticalEvent;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RealtimeEventTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);
    }

    #[Test]
    public function event_inventory_updated_dipicu_saat_barang_baru_dibuat()
    {
        Event::fake([InventoryUpdatedEvent::class]);

        $data = [
            'name' => 'Test Item',
            'part_number' => 'TEST-001',
            'brand' => 'Test Brand',
            'category' => 'Test Category',
            'location' => 'Tegal',
            'status' => 'aktif',
            'condition' => 'Baik',
            'age' => 'Baru',
            'type' => 'asset',
            'stock' => 10,
            'unit' => 'Unit',
        ];

        $this->actingAs($this->user)->post(route('inventory.store'), $data);

        Event::assertDispatched(InventoryUpdatedEvent::class, function ($event) {
            return $event->sparepart->part_number === 'TEST-001' && $event->action === 'created';
        });
    }

    #[Test]
    public function event_stock_critical_dipicu_saat_stok_di_bawah_minimum()
    {
        Event::fake([StockCriticalEvent::class]);

        $sparepart = Sparepart::factory()->create([
            'stock' => 10,
            'minimum_stock' => 5,
        ]);

        // Update stok menjadi 2 (di bawah 50% dari minimum 5)
        $this->actingAs($this->user)->put(route('inventory.update', $sparepart->id), [
            'name' => $sparepart->name,
            'part_number' => $sparepart->part_number,
            'brand' => $sparepart->brand,
            'category' => $sparepart->category,
            'location' => $sparepart->location,
            'stock' => 2,
            'minimum_stock' => 5,
            'condition' => 'Baik',
            'status' => 'aktif',
            'type' => 'asset',
            'age' => 'Baru',
        ]);

        Event::assertDispatched(StockCriticalEvent::class, function ($event) {
            return $event->severity === 'critical' || $event->severity === 'depleted';
        });
    }
}
