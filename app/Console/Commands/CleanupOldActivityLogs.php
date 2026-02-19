<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupOldActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup {--months=6 : Hapus log lebih dari X bulan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup activity logs older than specified months (default: 6 months)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $months = (int) $this->option('months');
        $cutoffDate = now()->subMonths($months);
        
        $this->info("Menghapus activity logs lebih tua dari {$months} bulan (sebelum {$cutoffDate->format('Y-m-d')})...");
        
        $count = \App\Models\ActivityLog::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("✅ Berhasil menghapus {$count} activity logs.");
        
        return Command::SUCCESS;
    }
}
