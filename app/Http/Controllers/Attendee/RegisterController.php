<?php

namespace App\Http\Controllers\Attendee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OtpService;
use App\Models\Attendee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Mail\SendRegistrationPassMail;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    protected OtpService $otp;

    public function __construct(OtpService $otp)
    {
        $this->otp = $otp;
    }

    /**
     * STEP 1: SEND OTP (Registration Purpose)
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150',
            'mobile'   => 'required|digits:10',
            'category' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $v = $validator->validated();

        try {
            $this->otp->sendOtp(
                $v['email'],
                $v['mobile'],
                'registration'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors'  => [
                    'email' => 'Too many OTP attempts. Try again later.'
                ]
            ], 429);
        }

        return response()->json(['success' => true]);
    }

    /**
     * STEP 2: VERIFY OTP + GENERATE PASS
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150',
            'mobile'   => 'required|digits:10',
            'category' => 'required|string',
            'otp'      => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $v = $validator->validated();

        // Verify with purpose-based OTP
        $valid = $this->otp->verifyOtp(
            $v['email'],
            $v['mobile'],
            $v['otp'],
            'registration'
        );

        if (! $valid) {
            return response()->json([
                'success' => false,
                'errors'  => ['otp' => 'Invalid or expired OTP.']
            ], 400);
        }

        /**
         * CREATE OR UPDATE ATTENDEE
         */
        $attendee = Attendee::firstOrNew(['email' => $v['email']]);

        $attendee->name       = $v['name'];
        $attendee->mobile     = $v['mobile'];
        $attendee->category   = $v['category'];
        $attendee->is_verified = true;

        if (! $attendee->pass_id) {
            $attendee->pass_id = Attendee::generatePassId();
        }

        $attendee->save();

        /**
         * GENERATE QR CODE + SAVE
         */
        $qrPng = QrCode::format('png')
            ->size(500)
            ->errorCorrection('H')
            ->generate($attendee->pass_id);

        $qrPath = "passes/qr_{$attendee->id}.png";

        Storage::disk('public')->put($qrPath, $qrPng);

        $attendee->qr_path = $qrPath;
        $attendee->save();

        $qrBase64 = base64_encode($qrPng);

        /**
         * GENERATE PDF PASS
         */
        $pdf = Pdf::loadView('pdf.registration-pass', [
            'attendee' => $attendee,
            'qrBase64' => $qrBase64
        ]);

        $pdfPath = "passes/pass_{$attendee->id}.pdf";

        Storage::disk('public')->put($pdfPath, $pdf->output());

        /**
         * SEND EMAIL WITH PDF ATTACHED
         */
        Mail::to($attendee->email)->queue(
            new SendRegistrationPassMail($attendee, $pdfPath)
        );

        /**
         * RETURN JSON RESPONSE (Alpine.js expects this)
         */
        return response()->json([
            'success' => true,
            'qr_url'  => 'data:image/png;base64,' . $qrBase64,
            'attendee' => [
                'name'     => $attendee->name,
                'email'    => $attendee->email,
                'mobile'   => $attendee->mobile,
                'category' => $attendee->category,
                'pass_id'  => $attendee->pass_id,
            ]
        ]);
    }
}
