<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoachAccountMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $tempPassword,
        public string $setupUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Sports Development Office coach account');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.coach-account');
    }
}
