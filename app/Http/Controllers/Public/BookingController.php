<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\ScreenSlotAssignment;
use App\Mail\BookingConfirmationMail;

class BookingController extends Controller
{
    public function confirm(Request $request, $assignmentId)
    {
        // 1) Validate seats
        $request->validate([
            "seats" => "required|array|min:1"
        ]);

        $seatIds = array_map('intval', $request->seats);

        // 2) Validate assignment
        $assignment = ScreenSlotAssignment::findOrFail($assignmentId);

        // 3) Attendee session check
        $attendeeId = session('public_attendee_id');
        if (!$attendeeId) {
            return response()->json([
                "success" => false,
                "message" => "Session expired. Please verify again."
            ], 401);
        }

        // 4) Double-booking check
        $alreadyBooked = BookingSeat::where('assignment_id', $assignmentId)
            ->whereIn('seat_id', $seatIds)
            ->pluck('seat_id')
            ->toArray();

        if (!empty($alreadyBooked)) {
            return response()->json([
                "success" => false,
                "message" => "Some seats are already booked.",
                "failed_seats" => $alreadyBooked
            ], 409);
        }

        // 5) Create booking + seats
        $booking = DB::transaction(function () use ($assignmentId, $seatIds, $attendeeId) {

            $booking = Booking::create([
                'attendee_id'   => $attendeeId,
                'assignment_id' => $assignmentId,
                'total_amount'  => 0,
                'currency'      => 'INR',
                'status'        => 'paid',
            ]);

            foreach ($seatIds as $seatId) {
                BookingSeat::create([
                    'booking_id'    => $booking->id,
                    'assignment_id' => $assignmentId,
                    'seat_id'       => $seatId,
                    'price'         => 0,
                    'status'        => 'booked'
                ]);
            }

            return $booking;
        });


        /*
        |--------------------------------------------------------------------------
        | 6) REUSE EXISTING QR GENERATION LOGIC
        |--------------------------------------------------------------------------
        */
        try {
            $dir = 'qrs';
            Storage::disk('public')->makeDirectory($dir);

            $filename = "booking_{$booking->id}.png";
            $relative = "{$dir}/{$filename}";
            $fullPath = storage_path("app/public/{$relative}");

            // Your registration QR logic EXACTLY:
            $qrPayload = url("/public/booking/verify/{$booking->id}");

            \QrCode::format('png')
                ->size(300)
                ->generate($qrPayload, $fullPath);

            $booking->qr_path = $relative;
            $booking->save();

        } catch (\Throwable $e) {
            report($e);
        }


        /*
        |--------------------------------------------------------------------------
        | 7) REUSE YOUR EXISTING PDF GENERATION LOGIC FROM REGISTRATION
        |--------------------------------------------------------------------------
        */
        try {
            // Your registration PDF generator likely uses:
            // PDF::loadView('pdf.ticket', [...]);
            // Or a helper: $this->generateTicketPdf($booking);

            $dir = 'tickets';
            Storage::disk('public')->makeDirectory($dir);

            $pdfFilename = "booking_{$booking->id}.pdf";
            $pdfPath = "{$dir}/{$pdfFilename}";

            // EXACT SAME LOGIC YOU USED FOR REGISTRATION PDF:
            $pdf = \PDF::loadView('pdf.ticket', [
                'booking' => $booking
            ]);

            Storage::disk('public')->put($pdfPath, $pdf->output());

            $booking->pdf_path = $pdfPath;
            $booking->save();

        } catch (\Throwable $e) {
            report($e);
        }


        /*
        |--------------------------------------------------------------------------
        | 8) Send confirmation email (includes QR + PDF)
        |--------------------------------------------------------------------------
        */
        try {
            Mail::to($booking->attendee->email)
                ->send(new BookingConfirmationMail($booking));
        } catch (\Throwable $e) {
            report($e);
        }


        // 9) Respond to UI
        return response()->json([
            "success" => true,
            "booking_id" => $booking->id
        ]);
    }
}
