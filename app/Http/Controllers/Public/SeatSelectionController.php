<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ScreenSlotAssignment;
use App\Models\Seat;
use App\Models\BookingSeat;

class SeatSelectionController extends Controller
{
    public function seats($assignmentId)
    {
        // 1. Load the showtime assignment
        $assignment = ScreenSlotAssignment::findOrFail($assignmentId);

        // 2. Load all seats for this screen
        $seats = Seat::where('screen_id', $assignment->screen_id)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();

        // 3. Get seats already booked for THIS assignment/showtime
        $bookedSeats = BookingSeat::where('assignment_id', $assignmentId)
            ->pluck('seat_id')
            ->toArray();

        // 4. Locked seats – DISABLED because Redis is not installed yet
        // IMPORTANT: When Redis is installed, we will replace this with real locking
        $lockedSeats = [];

        // 5. Format output: available / booked / locked
        $result = $seats->map(function ($seat) use ($bookedSeats, $lockedSeats) {

            if (in_array($seat->id, $bookedSeats)) {
                $status = 'booked';
            } elseif (in_array($seat->id, $lockedSeats)) {
                $status = 'locked';
            } else {
                $status = 'available';
            }

            return [
                'id'          => $seat->id,
                'row_label'   => $seat->row_label,
                'seat_number' => $seat->seat_number,
                'seat_code'   => $seat->seat_code,
                'status'      => $status,
            ];
        });

        return response()->json([
            'success' => true,
            'seats' => $result,
        ]);
    }
}
