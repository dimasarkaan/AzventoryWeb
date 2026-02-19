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

    // Buat instance notifikasi baru.
    public function __construct($title, $downloadUrl)
    {
        $this->title = $title;
        $this->downloadUrl = $downloadUrl;
    }

    // Tentukan channel pengiriman notifikasi.
    public function via(object $notifiable): array
    {
        // Mendukung database (in-app) dan broadcast (real-time wrapper)
        return ['database', 'broadcast'];
    }

    // Representasi email dari notifikasi.
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Laporan Siap Diunduh - Azventory')
                    ->line('Laporan Anda "' . $this->title . '" telah selesai dibuat.')
                    ->action('Unduh Laporan', url($this->downloadUrl))
                    ->line('Tautan ini akan valid selama file masih tersimpan di server.');
    }

    // Representasi array dari notifikasi.
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

    // Representasi broadcast dari notifikasi.
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
