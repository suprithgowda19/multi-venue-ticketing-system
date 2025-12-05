<?php

namespace App\Services;

use App\Models\AttendeeOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;   // ✅ REQUIRED
use App\Mail\SendOtpMail;
use Exception;

class OtpService
{
    protected int $expiryMinutes = 3;        // OTP valid for 3 minutes
    protected int $maxOtpRequests = 5;       // rate limit
    protected int $rateWindowMinutes = 30;   // rate window
    protected int $maxAttempts = 5;          // verify attempts

    /**
     * Send OTP for a given identity & purpose.
     */
    public function sendOtp(?string $identityEmail, ?string $identityMobile, string $purpose = 'registration'): bool
    {
        if (empty($identityEmail) && empty($identityMobile)) {
            throw new Exception("missing_identity");
        }

        // Rate limit based on email OR mobile
        $this->rateLimit($identityEmail, $identityMobile, $purpose);

        // Remove older OTPs for same identity+pupose
        $this->deleteExisting($identityEmail, $identityMobile, $purpose);

        // Generate OTP
        $otp = random_int(100000, 999999);

        // Store hashed OTP
        AttendeeOtp::create([
            'email'      => $identityEmail,
            'mobile'     => $identityMobile,
            'purpose'    => $purpose,
            'otp_hash'   => Hash::make((string)$otp),
            'expires_at' => now()->addMinutes($this->expiryMinutes),
        ]);

        // EMAIL DELIVERY
        if ($identityEmail) {
            try {
                Mail::to($identityEmail)->queue(new SendOtpMail($otp, $purpose));
            } catch (\Throwable $e) {
                Log::error("OTP email failed: " . $e->getMessage());   // ✅ Now works
            }
        }

        // FUTURE: SMS
        // $this->sendSmsOtp($identityMobile, $otp, $purpose);

        // FUTURE: WhatsApp
        // $this->sendWhatsappOtp($identityMobile, $otp, $purpose);

        return true;
    }

    /**
     * Verify OTP for identity + purpose.
     */
    public function verifyOtp(?string $identityEmail, ?string $identityMobile, string $inputOtp, string $purpose = 'registration'): bool
    {
        if (empty($identityEmail) && empty($identityMobile)) {
            return false;
        }

        $entry = AttendeeOtp::when($identityEmail, fn($q) => $q->where('email', $identityEmail))
            ->when(!$identityEmail && $identityMobile, fn($q) => $q->where('mobile', $identityMobile))
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (! $entry) {
            return false;
        }

        if ($entry->isExpired()) {
            $entry->delete();
            return false;
        }

        if ($entry->attempts >= $this->maxAttempts) {
            $entry->delete();
            return false;
        }

        if (! Hash::check((string) $inputOtp, $entry->otp_hash)) {
            $entry->incrementAttempts();
            return false;
        }

        // Consume OTP
        $entry->delete();
        return true;
    }

    /**
     * Delete existing OTPs for identity + purpose.
     */
    protected function deleteExisting(?string $email, ?string $mobile, string $purpose): void
    {
        AttendeeOtp::when($email, fn($q) => $q->where('email', $email))
            ->when(!$email && $mobile, fn($q) => $q->where('mobile', $mobile))
            ->where('purpose', $purpose)
            ->delete();
    }

    /**
     * Rate limit check.
     */
    protected function rateLimit(?string $email, ?string $mobile, string $purpose): void
    {
        $query = AttendeeOtp::where('purpose', $purpose)
            ->where('created_at', '>=', now()->subMinutes($this->rateWindowMinutes));

        if ($email) {
            $query->where('email', $email);
        } elseif ($mobile) {
            $query->where('mobile', $mobile);
        }

        if ($query->count() >= $this->maxOtpRequests) {
            throw new Exception("otp_rate_limited");
        }
    }

    /**
     * FUTURE: SMS OTP
     */
    protected function sendSmsOtp(?string $mobile, int $otp, string $purpose = 'registration'): void
    {
        if (!$mobile) return;

        // Http::post('https://sms-provider/api', [...]);
    }

    /**
     * FUTURE: WhatsApp OTP
     */
    protected function sendWhatsappOtp(?string $mobile, int $otp, string $purpose = 'registration'): void
    {
        if (!$mobile) return;

        // Http::post('https://graph.facebook.com/...', [...]);
    }
}
