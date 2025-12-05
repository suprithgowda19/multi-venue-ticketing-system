<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'attendee_id',
        'assignment_id',
        'total_amount',
        'currency',
        'status',
        'payment_reference',
        'qr_path',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // The user who made the booking
    public function attendee()
    {
        return $this->belongsTo(Attendee::class);
    }

    // The movie + screen + slot + day assignment
    public function assignment()
    {
        return $this->belongsTo(ScreenSlotAssignment::class, 'assignment_id');
    }

    // Seats booked under this booking
    public function seats()
    {
        return $this->hasMany(BookingSeat::class);
    }
}
