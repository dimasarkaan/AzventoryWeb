<?php

namespace App\Notifications;

use App\Models\StockLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class StockRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public StockLog $stockLog, public string $message)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'stock_log_id' => $this->stockLog->id,
            'message' => $this->message,
            'url' => route('stock-approvals.index'),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Permintaan Stok Baru',
            'message' => $this->message,
            'url' => route('stock-approvals.index'),
            'unread_count' => $notifiable->unreadNotifications()->count() + 1,
        ]);
    }
}
