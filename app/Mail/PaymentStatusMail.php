<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;
    public string $statusType;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, string $statusType)
    {
        $this->payment = $payment;
        $this->statusType = $statusType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->statusType === 'verified'
            ? 'Payment Verified - TIMS'
            : 'Payment Rejected - TIMS';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-status',
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
