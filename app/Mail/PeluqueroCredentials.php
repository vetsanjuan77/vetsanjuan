<?php

namespace App\Mail;

use App\Models\Peluquero;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PeluqueroCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $peluquero;
    public $temporaryPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(Peluquero $peluquero, string $temporaryPassword)
    {
        $this->peluquero = $peluquero;
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Credenciales de Acceso - Panel de Peluquería VetSanJuan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.peluquero-credentials',
            with: [
                'peluquero' => $this->peluquero,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => url('/peluquero'),
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