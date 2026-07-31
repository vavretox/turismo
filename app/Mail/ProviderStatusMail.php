<?php

namespace App\Mail;

use App\Models\TourismServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProviderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly TourismServiceProvider $provider,
        public readonly ?string $temporaryPassword = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Estado de su registro turístico: '.$this->provider->commercial_name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.provider-status');
    }
}
