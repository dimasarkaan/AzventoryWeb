<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
        $this->operator = User::factory()->create(['role' => 'operator']);

        Notification::fake();
    }

    #[Test]
    public function superadmin_dapat_melihat_persetujuan_tertunda()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);

        // Seed Pending Log
        StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Restock',
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('inventory.stock-approvals.index'));

        $response->assertStatus(200);
        $response->assertSee('Restock');
        $response->assertSee($item->name);
    }

    #[Test]
    public function superadmin_dapat_menyetujui_permintaan_stok()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);

        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Restock',
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status' => 'approved',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check DB Status
        $this->assertDatabaseHas('stock_logs', [
            'id'          => $log->id,
            'status'      => 'approved',
            'approved_by' => $this->superAdmin->id,
        ]);

        // Check Inventory Update
        $this->assertDatabaseHas('spareparts', [
            'id'    => $item->id,
            'stock' => 15, // 10 + 5
        ]);

        // Check Notification Sent to Operator
        Notification::assertSentTo(
            [$this->operator],
            \App\Notifications\StockRequestNotification::class
        );
    }

    #[Test]
    public function superadmin_dapat_menolak_permintaan_stok()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);

        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Incorrect',
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status'           => 'rejected',
                'rejection_reason' => 'Alasan penolakan tidak valid.',
            ]);

        // Check DB
        $this->assertDatabaseHas('stock_logs', [
            'id'               => $log->id,
            'status'           => 'rejected',
            'rejection_reason' => 'Alasan penolakan tidak valid.',
        ]);

        // Check Inventory UNCHANGED
        $this->assertDatabaseHas('spareparts', [
            'id'    => $item->id,
            'stock' => 10,
        ]);
    }

    #[Test]
    public function reject_tanpa_alasan_gagal_validasi()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);

        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Restock',
            'status'       => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status'           => 'rejected',
                'rejection_reason' => '', // kosong — harus gagal
            ]);

        $response->assertSessionHasErrors('rejection_reason');

        // Stok log harus tetap pending
        $this->assertDatabaseHas('stock_logs', [
            'id'     => $log->id,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function reject_menyimpan_alasan_di_database()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);

        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Restock',
            'status'       => 'pending',
        ]);

        $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status'           => 'rejected',
                'rejection_reason' => 'Stok sudah cukup saat ini.',
            ]);

        $this->assertDatabaseHas('stock_logs', [
            'id'               => $log->id,
            'status'           => 'rejected',
            'rejection_reason' => 'Stok sudah cukup saat ini.',
        ]);
    }

    #[Test]
    public function pesan_success_approve_berbeda_dengan_reject()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);

        $logApprove = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Restock A',
            'status'       => 'pending',
        ]);

        $responseApprove = $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $logApprove->id), [
                'status' => 'approved',
            ]);
        $responseApprove->assertSessionHas('success', 'Pengajuan berhasil disetujui.');

        $logReject = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 3,
            'reason'       => 'Restock B',
            'status'       => 'pending',
        ]);

        $responseReject = $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $logReject->id), [
                'status'           => 'rejected',
                'rejection_reason' => 'Tidak perlu restock.',
            ]);
        $responseReject->assertSessionHas('success', 'Pengajuan berhasil ditolak.');
    }

    #[Test]
    public function halaman_approval_mendukung_search_nama_barang()
    {
        $item1 = Sparepart::factory()->create(['name' => 'Filter Oli Mesin']);
        $item2 = Sparepart::factory()->create(['name' => 'Busi Iridium']);

        StockLog::create(['sparepart_id' => $item1->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 5, 'reason' => 'Restock', 'status' => 'pending']);
        StockLog::create(['sparepart_id' => $item2->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 3, 'reason' => 'Restock', 'status' => 'pending']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('inventory.stock-approvals.index', ['search' => 'Filter Oli']));

        $response->assertStatus(200);
        $response->assertSee('Filter Oli Mesin');
        $response->assertDontSee('Busi Iridium');
    }

    #[Test]
    public function halaman_approval_mendukung_filter_jenis()
    {
        $item = Sparepart::factory()->create(['stock' => 20]);

        StockLog::create(['sparepart_id' => $item->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 5, 'reason' => 'Restock', 'status' => 'pending']);
        StockLog::create(['sparepart_id' => $item->id, 'user_id' => $this->operator->id, 'type' => 'keluar', 'quantity' => 2, 'reason' => 'Dipakai', 'status' => 'pending']);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('inventory.stock-approvals.index', ['filter_type' => 'masuk']));

        $response->assertStatus(200);
        $response->assertSee('Restock');
        $response->assertDontSee('Dipakai');
    }

    #[Test]
    public function rejection_reason_maksimal_500_karakter()
    {
        $item = Sparepart::factory()->create();
        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Mau restock',
            'status'       => 'pending',
        ]);

        $longReason = str_repeat('a', 501);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status'           => 'rejected',
                'rejection_reason' => $longReason,
            ]);

        $response->assertSessionHasErrors('rejection_reason');
    }

    #[Test]
    public function notifikasi_operator_mengandung_alasan_penolakan()
    {
        $item = Sparepart::factory()->create(['name' => 'Barang A']);
        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id'      => $this->operator->id,
            'type'         => 'masuk',
            'quantity'     => 5,
            'reason'       => 'Pengajuan stok',
            'status'       => 'pending',
        ]);

        $reason = 'Alasan penolakan spesifik.';

        $this->actingAs($this->superAdmin)
            ->put(route('inventory.stock-approvals.update', $log->id), [
                'status'           => 'rejected',
                'rejection_reason' => $reason,
            ]);

        // Verifikasi notifikasi terkirim dengan alasan yang benar
        Notification::assertSentTo(
            [$this->operator],
            \App\Notifications\StockRequestNotification::class,
            function ($notification) use ($reason) {
                return $notification->stockLog->rejection_reason === $reason && 
                       str_contains($notification->message, 'ditolak');
            }
        );
    }

    #[Test]
    public function filter_kombinasi_search_dan_jenis_berhasil()
    {
        $item1 = Sparepart::factory()->create(['name' => 'Oli Mesin']);
        $item2 = Sparepart::factory()->create(['name' => 'Oli Transmisi']);
        $item3 = Sparepart::factory()->create(['name' => 'Busi']);

        // Oli Mesin - Masuk
        StockLog::create(['sparepart_id' => $item1->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 5, 'reason' => 'Restock A', 'status' => 'pending']);
        // Oli Transmisi - Keluar
        StockLog::create(['sparepart_id' => $item2->id, 'user_id' => $this->operator->id, 'type' => 'keluar', 'quantity' => 2, 'reason' => 'Pakai B', 'status' => 'pending']);
        // Busi - Masuk
        StockLog::create(['sparepart_id' => $item3->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 10, 'reason' => 'Restock C', 'status' => 'pending']);

        // Cari "Oli" + Jenis "Masuk" -> Harusnya cuma muncul Oli Mesin
        $response = $this->actingAs($this->superAdmin)
            ->get(route('inventory.stock-approvals.index', [
                'search'      => 'Oli',
                'filter_type' => 'masuk'
            ]));

        $response->assertStatus(200);
        $response->assertSee('Oli Mesin');
        $response->assertDontSee('Oli Transmisi');
        $response->assertDontSee('Busi');
    }

    #[Test]
    public function halaman_approval_mendukung_filter_status_riwayat()
    {
        $item = Sparepart::factory()->create(['name' => 'Barang Riwayat']);
        
        // 1 Pending, 1 Approved, 1 Rejected
        StockLog::create(['sparepart_id' => $item->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 5, 'reason' => 'Pending Request', 'status' => 'pending']);
        StockLog::create(['sparepart_id' => $item->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 3, 'reason' => 'Approved Request', 'status' => 'approved', 'approved_by' => $this->superAdmin->id]);
        StockLog::create(['sparepart_id' => $item->id, 'user_id' => $this->operator->id, 'type' => 'masuk', 'quantity' => 2, 'reason' => 'Rejected Request', 'status' => 'rejected', 'approved_by' => $this->superAdmin->id, 'rejection_reason' => 'Alasan Tolak']);

        // Default: Pending only
        $responsePending = $this->actingAs($this->superAdmin)->get(route('inventory.stock-approvals.index'));
        $responsePending->assertSee('Pending Request');
        $responsePending->assertDontSee('Approved Request');
        $responsePending->assertDontSee('Rejected Request');

        // Filter: Approved
        $responseApproved = $this->actingAs($this->superAdmin)->get(route('inventory.stock-approvals.index', ['status' => 'approved']));
        $responseApproved->assertDontSee('Pending Request');
        $responseApproved->assertSee('Approved Request');
        $responseApproved->assertSee($this->superAdmin->name); // Check approver name visible

        // Filter: Rejected
        $responseRejected = $this->actingAs($this->superAdmin)->get(route('inventory.stock-approvals.index', ['status' => 'rejected']));
        $responseRejected->assertDontSee('Pending Request');
        $responseRejected->assertSee('Rejected Request');
        $responseRejected->assertSee('Alasan Tolak');

        // Filter: All Status (empty string)
        $responseAll = $this->actingAs($this->superAdmin)->get(route('inventory.stock-approvals.index', ['status' => '']));
        $responseAll->assertSee('Pending Request');
        $responseAll->assertSee('Approved Request');
        $responseAll->assertSee('Rejected Request');
    }
}
