<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LedgerDueReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ledger;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $ledger)
    {
        $this->user = $user;
        $this->ledger = $ledger;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ledger Payment Due Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.Ledger-Due',
            with: [
                'user' => $this->user,
                'ledger' => $this->ledger,
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
