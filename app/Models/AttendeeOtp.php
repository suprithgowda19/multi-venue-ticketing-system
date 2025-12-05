<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendeeOtp extends Model
{
    protected $fillable = [
        'attendee_id',   // IMPORTANT for linking OTP to attendee
        'email',
        'mobile',
        'purpose',       // registration, booking, etc.
        'otp_hash',
        'expires_at',
        'attempts',
        'used',          // mark OTP as consumed
        'request_ip',    // for security/audit
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    /**
     * Relationship: OTP belongs to an attendee
     */
    public function attendee()
    {
        return $this->belongsTo(Attendee::class);
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Increment failed attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }
}
