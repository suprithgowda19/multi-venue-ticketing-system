<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OtpService;
use App\Models\Attendee;

class PublicBookingOtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * STEP 1: REQUEST OTP FOR BOOKING
     */
    public function requestOtp(Request $request)
    {
        // Force JSON response for validation errors
        $request->headers->set('Accept', 'application/json');

        // 1) BASIC VALIDATION (no name required)
        $validated = $request->validate([
            'email'  => 'required|email|exists:attendees,email',
            'mobile' => 'required|digits:10|exists:attendees,mobile',
        ]);

        // 2) CHECK EMAIL + MOBILE BELONG TO SAME ATTENDEE
        $attendee = Attendee::where('email', $request->email)
            ->where('mobile', $request->mobile)
            ->first();

        if (! $attendee) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'email'  => ['Email does not match this mobile number.'],
                    'mobile' => ['Mobile does not match this email.']
                ]
            ], 422);
        }

        // 3) SEND OTP
        try {
            $this->otpService->sendOtp(
                $attendee->email,
                $attendee->mobile,
                'booking'
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'errors'  => [
                    'otp' => ['Unable to send OTP. Try again later.']
                ]
            ], 500);
        }

        // 4) SAVE SESSION
        session([
            'public_attendee_id'         => $attendee->id,
            'public_booking_otp_sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ]);
    }

    /**
     * STEP 2: VERIFY OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        $validated = $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $attendeeId = session('public_attendee_id');

        if (! $attendeeId) {
            return response()->json([
                'success' => false,
                'errors'  => ['session' => ['Session expired. Request new OTP.']]
            ], 440);
        }

        $attendee = Attendee::find($attendeeId);

        if (! $attendee) {
            return response()->json([
                'success' => false,
                'errors'  => ['identity' => ['Attendee not found.']]
            ], 404);
        }

        // Verify OTP
        $valid = $this->otpService->verifyOtp(
            $attendee->email,
            $attendee->mobile,
            $request->otp,
            'booking'
        );

        if (! $valid) {
            return response()->json([
                'success' => false,
                'errors'  => ['otp' => ['Invalid or expired OTP.']]
            ], 422);
        }

        // SUCCESS
        session([
            'public_attendee_verified'     => true,
            'public_attendee_verified_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified.',
        ]);
    }
}
