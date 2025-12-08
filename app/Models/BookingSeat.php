<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    protected $fillable = [
        'booking_id',
        'assignment_id',
        'seat_id',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function assignment()
    {
        return $this->belongsTo(ScreenSlotAssignment::class, 'assignment_id');
    }
}
