<?php

namespace Tests\Feature\General;

use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\OverdueBorrowingNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TesPerintahKonsol extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Command send-overdue-notifications mengirim notifikasi dengan benar.
     */
    public function test_command_kirim_notifikasi_terlambat_berjalan_dengan_benar()
    {
        Notification::fake();

        $user = User::factory()->create();
        $sparepart = Sparepart::factory()->create();

        // Buat peminjaman yang sudah melewati batas waktu
        // Buat peminjaman yang sudah melewati batas waktu
        $overdue = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now()->subDays(2),
            'expected_return_at' => now()->subDay(),
            'notes' => 'Test overdue',
        ]);

        // Buat peminjaman yang masih aman (belum lewat batas)
        $notOverdue = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now()->subDay(),
            'expected_return_at' => now()->addDay(),
            'notes' => 'Test safe',
        ]);

        // Jalankan command
        $this->artisan('app:send-overdue-notifications')
            ->expectsOutput('Memeriksa peminjaman yang terlambat...')
            ->assertExitCode(0);

        // Verifikasi notifikasi dikirim ke user yang tepat
        Notification::assertSentTo(
            [$user],
            OverdueBorrowingNotification::class,
            function ($notification) {
                // Check if the notification has the borrowing property (or matches the structure)
                // Accessing protected/private might need reflection or just checking instance
                return $notification instanceof OverdueBorrowingNotification;
            }
        );

        // Verifikasi notifikasi TIDAK dikirim untuk peminjaman yang belum lewat batas
        Notification::assertNotSentTo(
            [$user],
            OverdueBorrowingNotification::class,
            function ($notification) use ($notOverdue) {
                return $notification->borrowing->id === $notOverdue->id;
            }
        );
    }

    /**
     * Test: Command logs:cleanup menghapus log lama.
     */
    public function test_command_cleanup_logs_menghapus_data_lama()
    {
        // Buat log lama (7 bulan lalu)
        ActivityLog::factory()->create([
            'created_at' => now()->subMonths(7),
            'description' => 'Old Log',
        ]);

        // Buat log baru
        ActivityLog::factory()->create([
            'created_at' => now(),
            'description' => 'New Log',
        ]);

        $this->assertEquals(2, ActivityLog::count());

        // Jalankan cleanup (default 6 bulan)
        $this->artisan('logs:cleanup')
            ->expectsOutput('Menghapus activity logs lebih tua dari 6 bulan (sebelum '.now()->subMonths(6)->format('Y-m-d').')...')
            ->assertExitCode(0);

        $this->assertEquals(1, ActivityLog::count());
        $this->assertDatabaseHas('activity_logs', ['description' => 'New Log']);
        $this->assertDatabaseMissing('activity_logs', ['description' => 'Old Log']);
    }

    /**
     * Test: Command activitylog:clean menghapus log berdasarkan hari.
     */
    public function test_command_activitylog_clean_menghapus_data_lama()
    {
        // Buat log lama (190 hari lalu)
        ActivityLog::factory()->create([
            'created_at' => now()->subDays(190),
            'description' => 'Very Old Log',
        ]);

        // Buat log baru
        ActivityLog::factory()->create([
            'created_at' => now(),
            'description' => 'Recent Log',
        ]);

        $this->assertEquals(2, ActivityLog::count());

        // Jalankan clean (default 180 hari)
        $this->artisan('activitylog:clean')
            ->expectsOutput('Berhasil menghapus 1 log aktivitas yang lebih lama dari 180 hari.')
            ->assertExitCode(0);

        $this->assertEquals(1, ActivityLog::count());
        $this->assertDatabaseHas('activity_logs', ['description' => 'Recent Log']);
        $this->assertDatabaseMissing('activity_logs', ['description' => 'Very Old Log']);
    }
}

