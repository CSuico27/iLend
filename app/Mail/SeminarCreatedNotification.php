<?php

namespace App\Mail;

use App\Models\SeminarSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SeminarCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $seminar;
    public $emailBody;
    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct(SeminarSchedule $seminar, string $emailBody, string $userName)
    {
        $this->seminar = $seminar;
        $this->emailBody = $emailBody;
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New seminar has been created for you!'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
           markdown: 'email.Created-Seminar', // This will be your Blade email template
            with: [
                'seminar' => $this->seminar,
                'emailBody' => $this->emailBody,
                'userName' => $this->userName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
