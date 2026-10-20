<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoachCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $coachData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($coachData)
    {
        $this->coachData = $coachData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to AthleteX - Coach Account Credentials')
                    ->view('emails.coach_credentials');
    }
}