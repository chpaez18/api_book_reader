<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tokenUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tokenUrl)
    {
        $this->tokenUrl = $tokenUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reset_password', [
            'url' => $this->tokenUrl
        ]);
    }
}