<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreenSlotAssignment extends Model
{
    protected $fillable = [
        'venue_id',
        'screen_id',
        'slot_id',
        'movie',
        'day',
        'status',
        'price_template_id',
    ];

    // Assignment belongs to a venue
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // Assignment belongs to a screen
    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    // Assignment belongs to a slot (time)
    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    // Assignment uses a pricing template
    public function priceTemplate()
    {
        return $this->belongsTo(PriceTemplate::class);
    }

    // A showtime has many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'assignment_id');
    }
}
