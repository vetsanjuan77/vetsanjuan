<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Sucursal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SucursalCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $sucursal;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password, Sucursal $sucursal)
    {
        $this->user = $user;
        $this->password = $password;
        $this->sucursal = $sucursal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Credenciales de Acceso - ' . $this->sucursal->nombre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sucursal-credentials',
            with: [
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'password' => $this->password,
                'sucursalNombre' => $this->sucursal->nombre,
                'sucursalCodigo' => $this->sucursal->codigo,
                'loginUrl' => url('/sucursal/login'),
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