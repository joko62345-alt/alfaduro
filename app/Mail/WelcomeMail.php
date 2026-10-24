<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Terima kasih telah bergabung!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notify',
            with: ['user' => $this->user],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function failed(Throwable $exception): void
    {
        Log::warning('Queued welcome email could not be sent.', [
            'user_id' => $this->user->id,
            'exception' => $exception,
        ]);
    }
}
