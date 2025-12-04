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
     * A screen has many assignments (movie schedules).
     */
    public function assignments()
    {
        return $this->hasMany(ScreenSlotAssignment::class);
    }

    /**
     * A screen has many attendees.
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    /**
     * Optional: If you want to quickly access slots through assignments
     */
    public function slots()
    {
        return $this->belongsToMany(Slot::class, 'screen_slot_assignments');
    }
}
