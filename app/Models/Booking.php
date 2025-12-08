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
        'metadata'     => 'array',
        'total_amount' => 'float',
    ];

    public function attendee()
    {
        return $this->belongsTo(Attendee::class);
    }

    public function assignment()
    {
        return $this->belongsTo(ScreenSlotAssignment::class, 'assignment_id');
    }

    public function seats()
    {
        return $this->hasMany(BookingSeat::class);
    }
}
