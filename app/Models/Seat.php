<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = [
        'screen_id',
        'row_label',
        'seat_number',
        'seat_code',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // A seat belongs to a screen
    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    // Seats can be referenced by many booking_seats
    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }
}
