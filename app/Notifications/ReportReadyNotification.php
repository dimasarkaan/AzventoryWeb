<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReadyNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected $title;

    protected $downloadUrl;

    // Tahap Persiapan: Menerima judul laporan dan alamat link download-nya.
    public function __construct($title, $downloadUrl)
    {
        $this->title = $title;
        $this->downloadUrl = $downloadUrl;
    }

    // Saluran Pengiriman: Disimpan ke database (ikon lonceng) dan dikirim Pop-up (Real-Time).
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Merakit kerangka dan isi pesan (Subjek, Sapaan, Link) yang akan dikirim ke Email pengguna.
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Laporan Siap Diunduh - '.config('app.name'))
            ->greeting('Halo,')
            ->line('Laporan Anda "'.$this->title.'" telah selesai dibuat.')
            ->action('Unduh Laporan', url($this->downloadUrl))
            ->line('Tautan ini akan valid selama file masih tersimpan di server.')
            ->salutation('Salam hangat,<br>**Tim '.config('app.name').'**');
    }

    // Merakit notifikasi "Sukses" untuk disimpan permanen di database.
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

    // Merakit pesan notifikasi yang akan muncul secara seketika saat laporan selesai dicetak.
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
