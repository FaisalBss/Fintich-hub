<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $otp;


    public function __construct($otp)
    {
        $this->otp = $otp;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OTP Code for Fintich Hub',
        );
    }


    public function content(): Content
    {

        return new Content(
            markdown: 'emails.auth.send-otp',
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
