<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'location',
    ];

    public function screens()
    {
        return $this->hasMany(Screen::class);
    }

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    public function assignments()
    {
        return $this->hasMany(ScreenSlotAssignment::class);
    }
}
