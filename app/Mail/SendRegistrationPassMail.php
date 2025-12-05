<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Attendee;
use Illuminate\Support\Facades\Storage;

class SendRegistrationPassMail extends Mailable
{
    use Queueable, SerializesModels;

    public Attendee $attendee;
    public string $pdfPath;

    public function __construct(Attendee $attendee, string $pdfPath)
    {
        $this->attendee = $attendee;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this
            ->subject('Your Festival Registration Pass')
            ->view('emails.attendee.pass-email')
            ->with(['attendee' => $this->attendee])
            ->attach(Storage::disk('public')->path($this->pdfPath), [
                'as'   => 'Festival-Pass.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
