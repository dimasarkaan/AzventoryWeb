<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use App\Notifications\OverdueBorrowingNotification;

class SendOverdueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-overdue-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users with overdue borrowings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue borrowings...');

        $overdueBorrowings = Borrowing::where('status', 'borrowed')
            ->where('expected_return_at', '<', now())
            ->with('user', 'sparepart')
            ->get();

        if ($overdueBorrowings->isEmpty()) {
            $this->info('No overdue borrowings found.');
            return;
        }

        foreach ($overdueBorrowings as $borrowing) {
            if ($borrowing->user) {
                // Send notification
                $borrowing->user->notify(new OverdueBorrowingNotification($borrowing));
                $this->info("Notification sent to {$borrowing->user->name} for {$borrowing->sparepart->name}");
            }
        }

        $this->info("Sent notifications for {$overdueBorrowings->count()} items.");
    }
}
