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
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }
}
