<?php

namespace App\Mail;

use App\Models\Sessao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SessaoConfirmadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Sessao $sessao) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sessão Agendada — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sessao-confirmada',
        );
    }
}
