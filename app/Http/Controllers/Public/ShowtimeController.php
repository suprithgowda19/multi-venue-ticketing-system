<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScreenSlotAssignment;

class ShowtimeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'day' => 'required|integer|min:1|max:7',
            'venue_id' => 'nullable|integer'
        ]);

        $day = $request->day;

        // ------------------------------------------------------
        // MODE 1 → Return all venues for selected day
        // ------------------------------------------------------
        if (!$request->has('venue_id')) {

            $venues = ScreenSlotAssignment::with('venue:id,name')
                ->where('day', $day)
                ->where('status', 'active')
                ->get()
                ->groupBy('venue_id')
                ->map(function ($assignments) {
                    return [
                        'venue_id' => $assignments->first()->venue_id,
                        'venue_name' => $assignments->first()->venue->name
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'venues' => $venues
            ]);
        }

        // ------------------------------------------------------
        // MODE 2 → Return movies for selected day + venue
        // ------------------------------------------------------
        $venueId = $request->venue_id;

        $assignments = ScreenSlotAssignment::with([
                'venue:id,name',
                'screen:id,venue_id,name',
                'slot:id,venue_id,start_time'
            ])
            ->where('day', $day)
            ->where('venue_id', $venueId)
            ->where('status', 'active')
            ->orderBy('movie')
            ->orderBy('slot_id')
            ->get();

        if ($assignments->isEmpty()) {
            return response()->json([
                'success' => true,
                'movies' => []
            ]);
        }

        // GROUP MOVIES
        $movies = [];

        foreach ($assignments as $a) {
            $movie = $a->movie;

            if (!isset($movies[$movie])) {
                $movies[$movie] = [
                    'movie' => $movie,
                    'showtimes' => []
                ];
            }

            $movies[$movie]['showtimes'][] = [
                'assignment_id' => $a->id,
                'screen_name' => $a->screen->name,
                'start_time' => $a->slot->start_time
            ];
        }

        return response()->json([
            'success' => true,
            'venue_id' => $venueId,
            'venue_name' => $assignments->first()->venue->name,
            'movies' => array_values($movies)
        ]);
    }
}
