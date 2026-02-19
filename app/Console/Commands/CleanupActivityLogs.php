<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    /**
     * Nama dan signature console command.
     *
     * @var string
     */
    protected $signature = 'activitylog:clean {--days=180 : Jumlah hari untuk menyimpan log}';

    protected $description = 'Bersihkan log aktivitas yang lebih lama dari jumlah hari yang ditentukan (default 180 hari / 6 bulan)';

    public function handle()
    {
        $days = $this->option('days');
        $date = now()->subDays($days);

        $count = \App\Models\ActivityLog::where('created_at', '<', $date)->delete();

        $this->info("Berhasil menghapus {$count} log aktivitas yang lebih lama dari {$days} hari.");
    }
}
