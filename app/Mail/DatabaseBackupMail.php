<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $backupPath;

    public $filename;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $backupPath, $filename)
    {
        $this->user = $user;
        $this->backupPath = $backupPath;
        $this->filename = $filename;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📦 Cadangan Database Azventory - '.now()->format('d F Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.database-backup',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->backupPath)
                ->as($this->filename)
                ->withMime('application/sql'),
        ];
    }
}
