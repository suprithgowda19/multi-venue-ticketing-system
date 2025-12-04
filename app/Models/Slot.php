<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
   protected $fillable = [
        'venue_id',
        'start_time',
    ];

    // For UI display
    public function getFormattedTimeAttribute()
    {
        return date('h:i A', strtotime($this->start_time));
    }

    /**
     * A slot belongs to a venue.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * A slot has many assignments (movie schedules).
     */
    public function assignments()
    {
        return $this->hasMany(ScreenSlotAssignment::class);
    }

    /**
     * A slot has many attendees.
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }
}
