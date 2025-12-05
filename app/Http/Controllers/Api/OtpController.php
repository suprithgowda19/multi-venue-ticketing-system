<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OtpService;
use Validator;

class OtpController extends Controller
{
    protected OtpService $otp;

    public function __construct(OtpService $otp)
    {
        $this->otp = $otp;
    }

    /**
     * GENERIC OTP SENDER
     * POST /api/otp/send
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => 'nullable|email',
            'mobile'  => 'nullable|digits:10',
            'purpose' => 'required|string|max:50', // registration, login, admin_login, etc.
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            $this->otp->sendOtp(
                $data['email'] ?? null,
                $data['mobile'] ?? null,
                $data['purpose']
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $msg = $e->getMessage() === 'otp_rate_limited'
                ? 'Too many OTP requests. Try again later.'
                : 'Unable to send OTP. Try again.';

            return response()->json([
                'success' => false,
                'errors' => ['identity' => $msg],
            ], 429);
        }
    }

    /**
     * GENERIC OTP VERIFIER
     * POST /api/otp/verify
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => 'nullable|email',
            'mobile'  => 'nullable|digits:10',
            'otp'     => 'required|digits:6',
            'purpose' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $valid = $this->otp->verifyOtp(
            $data['email'] ?? null,
            $data['mobile'] ?? null,
            $data['otp'],
            $data['purpose']
        );

        if (! $valid) {
            return response()->json([
                'success' => false,
                'errors'  => ['otp' => 'Invalid or expired OTP.'],
            ], 400);
        }

        return response()->json(['success' => true]);
    }
}
