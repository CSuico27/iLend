<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentStatus extends Mailable
{
    use Queueable, SerializesModels;
    public Payment $payment;
    public string $status;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, string $status)
    {
        $this->payment = $payment;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Status',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.Payment-Status',
            with: [
                'payment' => $this->payment,
                'status' => $this->status,
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
        if ($this->status === 'Approved' && $this->payment->receipt) {
            return [
                Attachment::fromPath(public_path('storage/' . $this->payment->receipt))
                    ->withMime('application/pdf'),
            ];
     }
        return [];
    }
}
