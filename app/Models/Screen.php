<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
    protected $fillable = [
        'venue_id',
        'name',
        'capacity',
    ];

    /**
     * A screen belongs to a venue.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * A screen has many seat records.
     * Used for seat-selection module.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * A screen has many movie showtime assignments.
     */
    public function assignments()
    {
        return $this->hasMany(ScreenSlotAssignment::class);
    }

    /**
     * A screen has many attendees (optional but supported).
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    /**
     * Optional:
     * Access slots through screen_slot_assignments.
     */
    public function slots()
    {
        return $this->belongsToMany(Slot::class, 'screen_slot_assignments', 'screen_id', 'slot_id');
    }
}
