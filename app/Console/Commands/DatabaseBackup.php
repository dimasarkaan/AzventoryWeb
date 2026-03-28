<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Enums\UserRole;
use App\Mail\DatabaseBackupMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pencadangan database otomatis dan kirim ke email Superadmin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pencadangan database...');
        ini_set('memory_limit', '512M'); // Tingkatkan memori untuk encoding lampiran

        $basename = "backup_" . config('app.name') . "_" . now()->format('Y-m-d_H-i-s');
        $sqlFilename = $basename . ".sql";
        $zipFilename = $basename . ".zip";
        
        $disk = Storage::disk('local');
        $path = $disk->path("backups/" . $sqlFilename);
        $zipPath = $disk->path("backups/" . $zipFilename);

        // Pastikan folder backup ada
        if (!$disk->exists('backups')) {
            $disk->makeDirectory('backups');
        }

        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");

        if ($connection === 'mysql') {
            // Perintah mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($path)
            );

            $output = [];
            $returnVar = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error('Gagal menjalankan mysqldump. Pastikan mysqldump terinstal di server.');
                Log::error('Backup DB Gagal: ' . implode("\n", $output));
                return 1;
            }
        } elseif ($connection === 'sqlite') {
            $dbPath = $dbConfig['database'];
            if (!file_exists($dbPath)) {
                $this->error("File database SQLite tidak ditemukan di: {$dbPath}");
                return 1;
            }
            copy($dbPath, $path);
        } else {
            $this->error("Koneksi '{$connection}' tidak didukung untuk pencadangan otomatis ini.");
            return 1;
        }

        $this->info("Backup database berhasil disimpan di: {$path}");

        // Kompres ke ZIP agar hemat memori saat dikirim
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $zip->addFile($path, $sqlFilename);
            $zip->close();
            @unlink($path); // Hapus file SQL mentah
        } else {
            $this->error('Gagal membuat file ZIP.');
            return 1;
        }

        // Kirim ke Superadmin
        $superadmins = User::where('role', UserRole::SUPERADMIN)->get();

        if ($superadmins->isEmpty()) {
            $this->warn('Tidak ada Superadmin untuk dikirimkan backup.');
            return 0;
        }

        foreach ($superadmins as $admin) {
            $this->info("Mengirim backup ke: {$admin->email}");
            Mail::to($admin->email)->send(new DatabaseBackupMail($admin, $zipPath, $zipFilename));
        }

        $this->info('Backup berhasil dikirim ke email.');

        return 0;
    }
}
