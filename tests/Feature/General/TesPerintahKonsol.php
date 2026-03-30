<?php

namespace Tests\Feature\General;

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
     * Test: Command app:backup-db berjalan dan mengirim email.
     */
    public function test_command_backup_database_berhasil_mengirim_email()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $disk = \Illuminate\Support\Facades\Storage::disk('local');
        if (! $disk->exists('backups')) {
            $disk->makeDirectory('backups');
        }

        // Trick: Jika database adalah :memory:, buat file dummy agar copy() di command tidak gagal
        $dbPath = config('database.connections.sqlite.database');
        if ($dbPath === ':memory:') {
            $tempDb = $disk->path('temp_test.sqlite');
            file_put_contents($tempDb, 'dummy sqlite content');
            config(['database.connections.sqlite.database' => $tempDb]);
        }

        // Pastikan ada superadmin
        User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);

        $this->artisan('app:backup-db')
            ->assertExitCode(0);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\DatabaseBackupMail::class);

        // Bersihkan
        $files = glob($disk->path('backups/backup_*.zip'));
        foreach ($files as $file) {
            @unlink($file);
        }
        if (isset($tempDb)) {
            @unlink($tempDb);
        }
    }

    /**
     * Test: Command reports:cleanup menghapus laporan dan backup lama.
     */
    public function test_command_cleanup_old_reports_dan_backups()
    {
        $diskPublic = \Illuminate\Support\Facades\Storage::disk('public');
        $diskLocal = \Illuminate\Support\Facades\Storage::disk('local');

        if (! $diskPublic->exists('reports')) {
            $diskPublic->makeDirectory('reports');
        }
        if (! $diskLocal->exists('backups')) {
            $diskLocal->makeDirectory('backups');
        }

        $oldReportRel = 'reports/old_test_report.xlsx';
        $oldBackupRel = 'backups/old_test_backup.sql';
        $newReportRel = 'reports/new_test_report.xlsx';

        $oldReportFull = $diskPublic->path($oldReportRel);
        $oldBackupFull = $diskLocal->path($oldBackupRel);
        $newReportFull = $diskPublic->path($newReportRel);

        file_put_contents($oldReportFull, 'old content');
        file_put_contents($oldBackupFull, 'old backup');
        file_put_contents($newReportFull, 'new content');

        // Set timestamp ke 60 hari yang lalu
        touch($oldReportFull, now()->subDays(60)->getTimestamp());
        touch($oldBackupFull, now()->subDays(60)->getTimestamp());

        $this->artisan('reports:cleanup', ['--days' => 30])
            ->assertExitCode(0);

        $this->assertFileDoesNotExist($oldReportFull, 'Laporan lama harus dihapus');
        $this->assertFileDoesNotExist($oldBackupFull, 'Backup lama harus dihapus');
        $this->assertFileExists($newReportFull, 'Laporan baru harus tetap ada');

        @unlink($newReportFull);
    }
}
