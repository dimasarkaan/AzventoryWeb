<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Borrowing;
use App\Notifications\OverdueBorrowingNotification;

class SendOverdueNotifications extends Command
{
    /**
     * Nama dan signature console command.
     *
     * @var string
     */
    protected $signature = 'app:send-overdue-notifications';

    /**
     * Deskripsi console command.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi ke user yang meminjam barang melewati batas waktu';

    /**
     * Eksekusi console command.
     */
    public function handle()
    {
        $this->info('Memeriksa peminjaman yang terlambat...');

        $overdueBorrowings = Borrowing::where('status', 'borrowed')
            ->where('expected_return_at', '<', now())
            ->with('user', 'sparepart')
            ->get();

        if ($overdueBorrowings->isEmpty()) {
            $this->info('Tidak ditemukan peminjaman yang terlambat.');
            return;
        }

        foreach ($overdueBorrowings as $borrowing) {
            if ($borrowing->user) {
                // Send notification
                $borrowing->user->notify(new OverdueBorrowingNotification($borrowing));
                $this->info("Notifikasi dikirim ke {$borrowing->user->name} untuk barang {$borrowing->sparepart->name}");
            }
        }

        $this->info("Mengirim notifikasi untuk {$overdueBorrowings->count()} item.");
    }
}
