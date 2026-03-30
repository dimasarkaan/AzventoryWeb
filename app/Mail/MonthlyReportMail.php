<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $attachmentsPaths;

    public $monthName;

    public $summary;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $attachmentsPaths, $monthName, $summary = [])
    {
        $this->user = $user;
        $this->attachmentsPaths = $attachmentsPaths;
        $this->monthName = $monthName;
        $this->summary = $summary;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Bulanan Azventory - '.$this->monthName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.monthly-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        foreach ($this->attachmentsPaths as $name => $path) {
            $attachments[] = Attachment::fromPath($path)
                ->as($name)
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }

        return $attachments;
    }
}
