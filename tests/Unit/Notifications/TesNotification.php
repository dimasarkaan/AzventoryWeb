<?php

namespace Tests\Unit\Notifications;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\ItemReturnedNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\MissingPriceNotification;
use App\Notifications\OverdueBorrowingNotification;
use App\Notifications\StockRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit test untuk kelas-kelas Notification.
 * Memastikan notifikasi mengandung data yang sesuai dan dikirim ke channel yang benar.
 */
class TesNotification extends TestCase
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

    #[Test]
    public function low_stock_notification_menangani_logika_stok_habis()
    {
        // Skenario: Stok Habis ( 0 )
        $sparepartEmpty = Sparepart::factory()->create(['name' => 'Mur', 'stock' => 0, 'minimum_stock' => 10]);
        $notifEmpty = new LowStockNotification($sparepartEmpty);
        $dataEmpty = $notifEmpty->toArray(new User());

        $this->assertEquals('Peringatan: Stok Habis!', $dataEmpty['title']);
        $this->assertEquals('danger', $dataEmpty['type']);
        $this->assertStringContainsString('telah HABIS (0)', $dataEmpty['message']);
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
    public function missing_price_notification_url_mengarah_ke_halaman_edit_dengan_focus_price()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $sparepart = Sparepart::factory()->create(['type' => 'sale']);
        $notification = new MissingPriceNotification($sparepart, $admin);
        $notifiable = User::factory()->make();

        $data = $notification->toArray($notifiable);

        $this->assertStringContainsString('/inventory/'.$sparepart->id.'/edit', $data['url']);
        $this->assertStringContainsString('?focus=price', $data['url']);
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
        $notifiable = User::factory()->make(['role' => UserRole::ADMIN]);

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

    // ── ItemReturnedNotification ────────────────────────────────
    
    #[Test]
    public function item_returned_notification_berisi_data_yang_benar()
    {
        $operator = User::factory()->create(['name' => 'Op Jajang']);
        $sparepart = Sparepart::factory()->create(['name' => 'Oli Mesin']);
        $borrowing = Borrowing::factory()->create([
            'user_id' => $operator->id,
            'sparepart_id' => $sparepart->id,
            'borrower_name' => $operator->name,
            'quantity' => 2
        ]);

        $notification = new ItemReturnedNotification($borrowing, 2, 'baik');
        $notifiable = User::factory()->make(['role' => UserRole::ADMIN]);

        $data = $notification->toArray($notifiable);

        $this->assertEquals('Barang Dikembalikan', $data['title']);
        $this->assertStringContainsString('Op Jajang', $data['message']);
        $this->assertStringContainsString('Oli Mesin', $data['message']);
        $this->assertStringContainsString('2', $data['message']);
        $this->assertStringContainsString('/inventory/borrow/'.$borrowing->id, $data['url']);
    }

    // ── OverdueBorrowingNotification ─────────────────────────────

    #[Test]
    public function overdue_borrowing_notification_memiliki_parameter_highlight_overdue()
    {
        $borrowing = Borrowing::factory()->create();
        $notification = new OverdueBorrowingNotification($borrowing);

        $data = $notification->toArray(new User());

        $this->assertStringContainsString('?highlight=overdue', $data['url']);
    }
}

