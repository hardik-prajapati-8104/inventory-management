<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $items;
    public Admin $recipient;

    public function __construct(array $items, Admin $recipient)
    {
        $this->items = $items;
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        $companyName = Setting::get('company', 'name', 'Vehicle Spare Parts Inventory');
        $count = count($this->items);

        return new Envelope(
            subject: $count > 0
                ? "{$companyName}: {$count} item(s) need your attention"
                : "{$companyName}: Daily summary — all clear",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-digest',
            with: [
                'items' => $this->items,
                'recipientName' => $this->recipient->name,
            ],
        );
    }
}
