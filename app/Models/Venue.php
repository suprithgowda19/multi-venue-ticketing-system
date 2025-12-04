<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'location',
    ];

    /**
     * A venue has many screens.
     */
    public function screens()
    {
        return $this->hasMany(Screen::class);
    }

    /**
     * A venue has many attendees (optional but useful).
     */
    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    /**
     * A venue has many screen-slot assignments (movies scheduled).
     */
    public function assignments()
    {
        return $this->hasMany(ScreenSlotAssignment::class);
    }
}
    