<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $receiptData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $receiptData)
    {
        $this->receiptData = $receiptData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Booking Receipt - ' . $this->receiptData['booking']->payment->reference_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-receipt',
            with: [
                'booking' => $this->receiptData['booking'],
                'vehicleName' => $this->receiptData['vehicleName'],
                'vehicleImage' => $this->receiptData['vehicleImage'],
                'rentalDays' => $this->receiptData['rentalDays'],
                'companyName' => $this->receiptData['companyName'],
                'companyAddress' => $this->receiptData['companyAddress'],
                'companyPhone' => $this->receiptData['companyPhone'],
                'companyEmail' => $this->receiptData['companyEmail'],
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