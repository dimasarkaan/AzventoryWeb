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
        $directory = 'reports'; // storage/app/public/reports
        
        if (!Storage::disk('public')->exists($directory)) {
            $this->warn("Direktori '{$directory}' tidak ditemukan.");
            return self::FAILURE;
        }

        $files = Storage::disk('public')->files($directory);
        $count = 0;
        $now = now();

        $this->info("Memulai pembersihan laporan yang lebih tua dari {$days} hari...");

        foreach ($files as $file) {
            // Abaikan file .gitignore
            if (basename($file) === '.gitignore') {
                continue;
            }

            $lastModified = Carbon::createFromTimestamp(Storage::disk('public')->lastModified($file));

            if ($lastModified->diffInDays($now) >= $days) {
                Storage::disk('public')->delete($file);
                $this->line("Menghapus: {$file}");
                $count++;
            }
        }

        if ($count > 0) {
            $this->info("✅ Berhasil menghapus {$count} file laporan lama.");
        } else {
            $this->info("ℹ️ Tidak ada file laporan lama yang perlu dihapus.");
        }

        return self::SUCCESS;
    }
}
