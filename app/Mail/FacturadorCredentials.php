<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Sucursal;

class FacturadorCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $facturador;
    public $sucursal;
    public $password;
    public $panelUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $facturador, Sucursal $sucursal, string $password, string $panelUrl)
    {
        $this->facturador = $facturador;
        $this->sucursal = $sucursal;
        $this->password = $password;
        $this->panelUrl = $panelUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Credenciales de Acceso - Panel Facturador VetSanJuan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.facturador-credentials',
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
