<?php

namespace App\Mail;

use App\Models\Tutor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApproveTutorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tutor;
    /**
     * Create a new message instance.
     */
    public function __construct($tutor)
    {
        $this->tutor = $tutor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Tutor Approval Required')
            ->view('emails.approve-tutor')
            ->with([
                'tutor' => $this->tutor,
            ]);
    }
}
