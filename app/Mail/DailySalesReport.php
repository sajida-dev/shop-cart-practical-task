<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailySalesReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $date;
    public Collection $soldProducts;

    /**
     * Create a new message instance.
     */
    public function __construct(string $date, Collection $soldProducts)
    {
        $this->date = $date;
        $this->soldProducts = $soldProducts;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Daily Sales Report for {$this->date}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.daily_sales_report',
            with: [
                'date' => $this->date,
                'soldProducts' => $this->soldProducts,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
