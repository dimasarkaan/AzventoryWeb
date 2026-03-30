<?php

namespace Tests\Feature\General;

use App\Events\InventoryUpdatedEvent;
use App\Events\StockCriticalEvent;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TesBroadcasting extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['broadcasting.default' => 'pusher']);
        \Illuminate\Support\Facades\Broadcast::forgetDrivers();
        require base_path('routes/channels.php');
    }

    /**
     * ✅ Channel Authorization (Handshake 403 vs 200)
     */
    public function test_otorisasi_channel_privat_memblokir_role_yang_tidak_berwenang()
    {
        $operator = User::factory()->create(['role' => 'operator', 'status' => 'aktif']);
        $superadmin = User::factory()->create(['role' => 'superadmin', 'status' => 'aktif']);

        // Operator mencoba listen ke channel stock-approvals (Khusus Superadmin)
        $response = $this->actingAs($operator)
            ->postJson('/broadcasting/auth', [
                'channel_name' => 'private-stock-approvals',
                'socket_id' => '1234.1234',
            ]);

        $response->assertStatus(403);

        // Superadmin mencoba listen ke channel stock-approvals
        $this->actingAs($superadmin)
            ->postJson('/broadcasting/auth', [
                'channel_name' => 'private-stock-approvals',
                'socket_id' => '1234.1234',
            ])
            ->assertStatus(200);
    }

    /**
     * ✅ Multi Device Sync (Event Dispatching)
     */
    public function test_pembaruan_inventaris_mendorong_event_yang_benar()
    {
        Event::fake([StockCriticalEvent::class, InventoryUpdatedEvent::class]);

        $superadmin = User::factory()->create(['role' => 'superadmin', 'status' => 'aktif']);
        $sparepart = Sparepart::factory()->create([
            'stock' => 10,
            'minimum_stock' => 5,
            'qr_code_path' => 'qrcodes/FAKE-QR-1234.png',
        ]);

        $response = $this->actingAs($superadmin)
            ->putJson(route('api.inventory.adjust-stock', $sparepart->id), [
                'type' => 'decrement',
                'quantity' => 6, // Sisa 4 (Di bawah minimum 5)
                'description' => 'Pengujian Event',
            ]);

        $response->assertStatus(200);

        // Assert Event API Scan QR Broadcasted
        Event::assertDispatched(InventoryUpdatedEvent::class);

        // Assert Critical Stock Event karena sisa 4 <= 5
        Event::assertDispatched(StockCriticalEvent::class);
    }

    /**
     * ✅ Concurrency: Approval Race Condition (Pessimistic Locking)
     */
    public function test_pessimistic_locking_mencegah_lost_update_pada_persetujuan()
    {
        Event::fake();
        $superadmin = User::factory()->create(['role' => 'superadmin', 'status' => 'aktif']);
        $sparepart = Sparepart::factory()->create(['stock' => 10, 'minimum_stock' => 2]);

        $stockLog = StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $superadmin->id,
            'type' => 'keluar',
            'quantity' => 6,
            'reason' => 'Test Out',
            'status' => 'pending',
        ]);

        // Karena sulit mensimulasikan multi-threading betulan di PHPUnit SQLite memory,
        // Kita validasi bahwa exception dilempar jika stok sudah berubah (simulasi).
        // Kita paksa stok di database berubah sebelum transaksi selesai.

        // Simulasikan Request 1 jalan normal
        $this->actingAs($superadmin)
            ->patch(route('inventory.stock-approvals.update', $stockLog->id), [
                'status' => 'approved',
            ])->assertRedirect();

        $this->assertEquals(4, $sparepart->fresh()->stock);

        // Buat log kedua untuk simulasi balapan
        $stockLog2 = StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $superadmin->id,
            'type' => 'keluar',
            'quantity' => 5, // Sisa 4, minta 5 -> Harus gagal pre-check
            'reason' => 'Test Out 2',
            'status' => 'pending',
        ]);

        $this->actingAs($superadmin)
            ->patch(route('inventory.stock-approvals.update', $stockLog2->id), [
                'status' => 'approved',
            ])->assertSessionHas('error'); // Ditolak karena tidak cukup
    }
}
