<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePublicBookingVerified
{
    public function handle(Request $request, Closure $next)
    {
        $attendeeId = session('public_attendee_id');
        $verified   = session('public_attendee_verified');

        if (! $attendeeId || ! $verified) {
            return response()->json([
                'success' => false,
                'message' => 'Booking verification required.',
                'code'    => 'NOT_VERIFIED'
            ], 401);
        }

        if ($time = session('public_attendee_verified_at')) {
            if (now()->diffInMinutes($time) > 30) {

                session()->forget([
                    'public_attendee_id',
                    'public_attendee_verified',
                    'public_attendee_verified_at'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Verification expired. Please verify OTP again.',
                    'code'    => 'VERIFICATION_EXPIRED'
                ], 440);
            }
        }

        return $next($request);
    }
}
