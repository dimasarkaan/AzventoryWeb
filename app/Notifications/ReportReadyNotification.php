<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ReportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $downloadUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $downloadUrl)
    {
        $this->title = $title;
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Support database (in-app) and broadcast (real-time wrapper)
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Laporan Siap Diunduh - Azventory')
                    ->line('Laporan Anda "' . $this->title . '" telah selesai dibuat.')
                    ->action('Unduh Laporan', url($this->downloadUrl))
                    ->line('Tautan ini akan valid selama file masih tersimpan di server.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Laporan Siap!',
            'message' => 'Laporan "' . $this->title . '" siap diunduh.',
            'url' => $this->downloadUrl,
            'icon' => 'document-text', // Frontend can use this to show icon
            'type' => 'success'
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Laporan Siap!',
            'message' => 'Laporan "' . $this->title . '" siap diunduh.',
            'url' => $this->downloadUrl,
            'type' => 'success',
        ]);
    }
}
