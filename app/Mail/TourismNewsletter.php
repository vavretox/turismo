<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use App\Models\Noticia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TourismNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Noticia $noticia,
        public NewsletterSubscriber $subscriber,
        public bool $isTest = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: ($this->isTest ? '[PRUEBA] ' : '').'Turismo Tarija: '.$this->noticia->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.tourism-newsletter');
    }
}
