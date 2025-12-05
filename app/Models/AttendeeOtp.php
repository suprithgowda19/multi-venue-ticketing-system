<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendeeOtp extends Model
{
    protected $fillable = [
        'email',
        'mobile',
        'purpose',
        'otp_hash',
        'expires_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }
}
