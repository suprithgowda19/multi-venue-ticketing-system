<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $mail = $this->subject('Your Ticket Booking Confirmation')
                     ->view('emails.booking-confirmation')
                     ->with([
                         'booking' => $this->booking
                     ]);

        /*
        |--------------------------------------------------------------------------
        | ATTACH QR IMAGE (if exists)
        |--------------------------------------------------------------------------
        */
        if ($this->booking->qr_path && Storage::disk('public')->exists($this->booking->qr_path)) {
            $qrFullPath = Storage::disk('public')->path($this->booking->qr_path);

            $mail->attach($qrFullPath, [
                'as'   => "QR-{$this->booking->id}.png",
                'mime' => 'image/png',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ATTACH PDF TICKET (if exists)
        |--------------------------------------------------------------------------
        */
        if ($this->booking->pdf_path && Storage::disk('public')->exists($this->booking->pdf_path)) {
            $pdfFullPath = Storage::disk('public')->path($this->booking->pdf_path);

            $mail->attach($pdfFullPath, [
                'as'   => "Ticket-{$this->booking->id}.pdf",
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
