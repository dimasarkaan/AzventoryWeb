<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Notifications\MissingPriceNotification;
use App\Notifications\StockRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit test untuk kelas-kelas Notification.
 * Memastikan notifikasi mengandung data yang sesuai dan dikirim ke channel yang benar.
 */
class TesKelasNotifikasi extends TestCase
{
    use RefreshDatabase;

    // ── LowStockNotification ─────────────────────────────────────

    #[Test]
    public function low_stock_notification_berisi_data_yang_benar()
    {
        $sparepart = Sparepart::factory()->create(['name' => 'Baut Khusus M5']);
        $notification = new LowStockNotification($sparepart);
        $notifiable = User::factory()->make(['role' => UserRole::ADMIN]);

        $data = $notification->toArray($notifiable);

        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertStringContainsString('Baut Khusus M5', $data['message']);
        $this->assertEquals($sparepart->id, $data['sparepart_id']);
    }

    #[Test]
    public function low_stock_notification_dikirim_melalui_channel_database_dan_broadcast()
    {
        $sparepart = Sparepart::factory()->create();
        $notification = new LowStockNotification($sparepart);
        $notifiable = User::factory()->make();

        $this->assertEquals(['database', 'broadcast'], $notification->via($notifiable));
    }

    // ── MissingPriceNotification ─────────────────────────────────

    #[Test]
    public function missing_price_notification_berisi_data_yang_benar()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN, 'name' => 'Admin Budi']);
        $sparepart = Sparepart::factory()->create(['name' => 'Sparepart Mahal', 'type' => 'sale']);
        $notification = new MissingPriceNotification($sparepart, $admin);
        $notifiable = User::factory()->make(['role' => UserRole::SUPERADMIN]);

        $data = $notification->toArray($notifiable);

        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('url', $data);
        $this->assertStringContainsString('Sparepart Mahal', $data['message']);
        $this->assertEquals($sparepart->id, $data['sparepart_id']);
        $this->assertEquals('Admin Budi', $data['added_by']);
    }

    #[Test]
    public function missing_price_notification_url_mengarah_ke_halaman_edit()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $sparepart = Sparepart::factory()->create(['type' => 'sale']);
        $notification = new MissingPriceNotification($sparepart, $admin);
        $notifiable = User::factory()->make();

        $data = $notification->toArray($notifiable);

        $this->assertStringContainsString('/inventory/'.$sparepart->id.'/edit', $data['url']);
    }

    // ── StockRequestNotification ─────────────────────────────────

    #[Test]
    public function stock_request_notification_berisi_message_dan_url_yang_benar()
    {
        $stockLog = StockLog::factory()->create([
            'type' => 'masuk',
            'status' => 'pending',
        ]);
        $notification = new StockRequestNotification($stockLog, 'Permintaan stok baru dari Operator');
        $notifiable = User::factory()->make();

        $data = $notification->toArray($notifiable);

        $this->assertEquals($stockLog->id, $data['stock_log_id']);
        $this->assertEquals('Permintaan stok baru dari Operator', $data['message']);
        $this->assertStringContainsString('stock-approvals', $data['url']);
    }

    #[Test]
    public function stock_request_notification_dikirim_melalui_channel_database_dan_broadcast()
    {
        $stockLog = StockLog::factory()->create(['type' => 'masuk', 'status' => 'pending']);
        $notification = new StockRequestNotification($stockLog, 'Pesan test');
        $notifiable = User::factory()->make();

        $this->assertEquals(['database', 'broadcast'], $notification->via($notifiable));
    }
}

