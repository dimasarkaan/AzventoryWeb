<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReadyNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $title;

    protected $downloadUrl;

    /**
     * Inisialisasi notifikasi untuk laporan yang telah selesai di-generate.
     */
    public function __construct($title, $downloadUrl)
    {
        $this->title = $title;
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Channel pengiriman: Database dan Real-time Broadcast.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Konten email notifikasi laporan siap unduh.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Laporan Siap Diunduh - Azventory')
            ->line('Laporan Anda "'.$this->title.'" telah selesai dibuat.')
            ->action('Unduh Laporan', url($this->downloadUrl))
            ->line('Tautan ini akan valid selama file masih tersimpan di server.');
    }

    /**
     * Data persistensi notifikasi dalam database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Laporan Siap!',
            'message' => 'Laporan "'.$this->title.'" siap diunduh.',
            'url' => $this->downloadUrl,
            'icon' => 'document-text',
            'type' => 'success',
        ];
    }

    /**
     * Pesan siaran real-time.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Laporan Siap!',
            'message' => 'Laporan "'.$this->title.'" siap diunduh.',
            'url' => $this->downloadUrl,
            'type' => 'success',
        ]);
    }
}
