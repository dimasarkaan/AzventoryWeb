<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activitylog:clean {--days=180 : Number of days to keep logs}';

    protected $description = 'Clean up activity logs older than specified days (default 180 days / 6 months)';

    public function handle()
    {
        $days = $this->option('days');
        $date = now()->subDays($days);

        $count = \App\Models\ActivityLog::where('created_at', '<', $date)->delete();

        $this->info("Deleted {$count} activity logs older than {$days} days.");
    }
}
