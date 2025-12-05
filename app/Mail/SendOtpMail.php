<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $otp;
    public string $purpose;

    public function __construct(int $otp, string $purpose = 'registration')
    {
        $this->otp = $otp;
        $this->purpose = $purpose;
    }

    public function build()
    {
        return $this
            ->subject('Your OTP Code')
            ->view('emails.attendee.send-otp')
            ->with([
                'otp'     => $this->otp,
                'purpose' => $this->purpose,
            ]);
    }
}
