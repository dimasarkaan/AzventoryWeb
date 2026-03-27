<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\ItemReturnedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesAuditLog extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function perubahan_peran_user_mencatat_log_audit_yang_detail()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $targetUser = User::factory()->create(['role' => UserRole::OPERATOR, 'name' => 'Budi Operator']);

        $response = $this->actingAs($superadmin)
            ->put(route('users.update', $targetUser), [
                'name' => 'Budi Operator',
                'email' => $targetUser->email,
                'role' => UserRole::ADMIN->value,
                'jabatan' => 'Staff IT',
                'status' => 'aktif',
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'User Diupdate',
            'user_id' => $superadmin->id,
        ]);

        $log = ActivityLog::latest()->first();
        $this->assertStringContainsString('Perubahan Peran', $log->description);
        $this->assertStringContainsString('Operator', $log->description);
        $this->assertStringContainsString('Admin', $log->description);
    }

    #[Test]
    public function ekspor_laporan_excel_mencatat_log_audit()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        
        // Mocking Excel export response to be fast
        $response = $this->actingAs($admin)
            ->get(route('reports.download', [
                'report_type' => 'inventory_list',
                'export_format' => 'excel',
                'period' => 'all'
            ]));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Laporan Diunduh (Excel)',
            'user_id' => $admin->id,
        ]);
        
        $log = ActivityLog::where('action', 'Laporan Diunduh (Excel)')->first();
        $this->assertStringContainsString('inventory_list', $log->description);
    }

    #[Test]
    public function pengembalian_barang_mengirimkan_notifikasi_ke_admin_dan_superadmin()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $operator = User::factory()->create(['role' => UserRole::OPERATOR]);

        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $borrowing = Borrowing::factory()->create([
            'user_id' => $operator->id,
            'sparepart_id' => $sparepart->id,
            'borrower_name' => $operator->name,
            'quantity' => 5,
            'status' => 'borrowed'
        ]);

        // Simulasikan pengembalian via InventoryService (trigger by controller)
        $this->actingAs($operator)
            ->post(route('inventory.borrow.return', $borrowing), [
                'return_quantity' => 5,
                'return_condition' => 'good',
                'return_notes' => 'Kembali aman',
                'return_photos' => [\Illuminate\Http\UploadedFile::fake()->image('bukti.jpg')]
            ]);

        Notification::assertSentTo(
            [$admin, $superadmin],
            ItemReturnedNotification::class
        );
    }
}
