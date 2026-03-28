<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupOldReports extends Command
{
    /**
     * Nama dan signature dari console command.
     *
     * @var string
     */
    protected $signature = 'reports:cleanup {--days=30 : Hapus laporan yang lebih tua dari X hari}';

    /**
     * Deskripsi console command.
     *
     * @var string
     */
    protected $description = 'Membersihkan file laporan PDF dan Excel lama di direktori storage';

    /**
     * Eksekusi console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $now = now();
        $totalDeleted = 0;

        // 1. Bersihkan Laporan di Disk Public
        $reportDir = 'reports';
        if (Storage::disk('public')->exists($reportDir)) {
            $files = Storage::disk('public')->files($reportDir);
            foreach ($files as $file) {
                if (basename($file) === '.gitignore') continue;
                $lastModified = Carbon::createFromTimestamp(Storage::disk('public')->lastModified($file));
                if ($lastModified->diffInDays($now) >= $days) {
                    Storage::disk('public')->delete($file);
                    $totalDeleted++;
                }
            }
        }

        // 2. Bersihkan Backup DB di Disk Local (storage/app/backups)
        $backupDir = 'backups';
        if (Storage::disk('local')->exists($backupDir)) {
            $files = Storage::disk('local')->files($backupDir);
            foreach ($files as $file) {
                if (basename($file) === '.gitignore') continue;
                $lastModified = Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file));
                if ($lastModified->diffInDays($now) >= $days) {
                    Storage::disk('local')->delete($file);
                    $totalDeleted++;
                }
            }
        }

        if ($totalDeleted > 0) {
            $this->info("✅ Berhasil menghapus {$totalDeleted} file lama (Laporan & Backup DB).");
        } else {
            $this->info("ℹ️ Tidak ada file lama yang perlu dihapus.");
        }

        return self::SUCCESS;
    }
}
