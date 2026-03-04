<?php

namespace Tests\Unit;

use App\Events\ActivityLogged;
use App\Events\BorrowingStatusChangedEvent;
use App\Events\InventoryUpdatedEvent;
use App\Events\StockCriticalEvent;
use App\Events\StockRequestEvent;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit test untuk semua kelas Event.
 * Memverifikasi channel name, broadcastAs, broadcastWith, dan konstruktor benar.
 */
class EventTest extends TestCase
{
    use RefreshDatabase;

    // ── StockCriticalEvent ───────────────────────────────────────

    #[Test]
    public function stock_critical_event_broadcast_ke_channel_stock_alerts()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 2, 'minimum_stock' => 10]);
        $event = new StockCriticalEvent($sparepart, 'critical');

        $channel = $event->broadcastOn();
        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('stock-alerts', $channel->name);
    }

    #[Test]
    public function stock_critical_event_broadcast_as_nama_yang_benar()
    {
        $sparepart = Sparepart::factory()->create();
        $event = new StockCriticalEvent($sparepart, 'warning');

        $this->assertEquals('StockCritical', $event->broadcastAs());
    }

    #[Test]
    public function stock_critical_event_broadcast_with_berisi_data_yang_benar()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 3, 'minimum_stock' => 10]);
        $event = new StockCriticalEvent($sparepart, 'critical');

        $data = $event->broadcastWith();

        $this->assertEquals($sparepart->id, $data['id']);
        $this->assertEquals($sparepart->name, $data['name']);
        $this->assertEquals('critical', $data['severity']);
        $this->assertEquals(30.0, $data['percentage']); // 3/10 * 100
    }

    #[Test]
    public function stock_critical_event_percentage_nol_jika_minimum_stock_nol()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5, 'minimum_stock' => 0]);
        $event = new StockCriticalEvent($sparepart, 'warning');

        $data = $event->broadcastWith();
        $this->assertEquals(0.0, $data['percentage']);
    }

    // ── InventoryUpdatedEvent ────────────────────────────────────

    #[Test]
    public function inventory_updated_event_broadcast_ke_channel_inventory_updates()
    {
        $sparepart = Sparepart::factory()->create();
        // Constructor: (Sparepart $sparepart, string $action, string $userName)
        $event = new InventoryUpdatedEvent($sparepart, 'created', 'Admin Budi');

        $channel = $event->broadcastOn();
        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('inventory-updates', $channel->name);
    }

    #[Test]
    public function inventory_updated_event_broadcast_with_berisi_action_dan_user()
    {
        $sparepart = Sparepart::factory()->create();
        $event = new InventoryUpdatedEvent($sparepart, 'updated', 'Superadmin');

        $data = $event->broadcastWith();
        $this->assertEquals('updated', $data['action']);
        $this->assertEquals('Superadmin', $data['user_name']);
        $this->assertArrayHasKey('message', $data);
    }

    // ── BorrowingStatusChangedEvent ──────────────────────────────

    #[Test]
    public function borrowing_status_changed_event_berisi_property_yang_benar()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $user = User::factory()->create();
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        // Constructor: (Borrowing $borrowing, string $oldStatus, string $newStatus, string $adminName)
        $event = new BorrowingStatusChangedEvent($borrowing, 'borrowed', 'returned', 'Admin Budi');

        $this->assertSame($borrowing, $event->borrowing);
        $this->assertEquals('borrowed', $event->oldStatus);
        $this->assertEquals('returned', $event->newStatus);
        $this->assertEquals('Admin Budi', $event->adminName);
    }

    #[Test]
    public function borrowing_status_changed_event_broadcast_ke_channel_inventory_updates()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $user = User::factory()->create();
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);

        $event = new BorrowingStatusChangedEvent($borrowing, 'borrowed', 'returned', 'Admin');
        $channel = $event->broadcastOn();

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals('inventory-updates', $channel->name);
    }

    // ── StockRequestEvent ────────────────────────────────────────

    #[Test]
    public function stock_request_event_broadcast_ke_private_channel_user()
    {
        $user = User::factory()->create();
        // Constructor: (User $user, string $message, string $url, int $unread_count)
        $event = new StockRequestEvent($user, 'Ada permintaan stok baru', '/url', 3);

        $channels = $event->broadcastOn();
        $firstChannel = is_array($channels) ? $channels[0] : $channels;

        $this->assertInstanceOf(PrivateChannel::class, $firstChannel);
        $this->assertStringContainsString((string) $user->id, $firstChannel->name);
    }

    #[Test]
    public function stock_request_event_broadcast_with_berisi_message_dan_url()
    {
        $user = User::factory()->create();
        $event = new StockRequestEvent($user, 'Permintaan dari operator', '/stock-approvals', 5);

        $data = $event->broadcastWith();

        $this->assertEquals('Permintaan dari operator', $data['message']);
        $this->assertEquals('/stock-approvals', $data['url']);
        $this->assertEquals(5, $data['unread_count']);
    }

    // ── ActivityLogged ───────────────────────────────────────────

    #[Test]
    public function activity_logged_event_berisi_data_log_yang_benar()
    {
        $user = User::factory()->create();
        $log = \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Test Action',
            'description' => 'Deskripsi test event.',
        ]);

        $event = new ActivityLogged($log);
        $this->assertSame($log, $event->log);

        $channels = $event->broadcastOn();
        $firstChannel = is_array($channels) ? $channels[0] : $channels;
        $this->assertNotNull($firstChannel);
    }
}
