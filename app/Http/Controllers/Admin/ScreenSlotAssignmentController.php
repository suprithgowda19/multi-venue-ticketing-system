<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreenSlotAssignment;
use App\Models\Venue;
use App\Models\Screen;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScreenSlotAssignmentController extends Controller
{
    public function index()
    {
        $assignments = ScreenSlotAssignment::with([
            'venue',
            'screen',
            'slot' // ❗ required for formatted_time
        ])
            ->orderBy('day')
            ->orderBy('venue_id')
            ->orderBy('screen_id')
            ->orderBy('slot_id')
            ->get();

        return view('admin.assignments.index', compact('assignments'));
    }


    public function create()
    {
        $venues = Venue::orderBy('name')->get();

        // Screens & Slots will be loaded dynamically via Alpine.js when venue is selected
        return view('admin.assignments.create', compact('venues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venue_id'  => 'required|exists:venues,id',
            'screen_id' => 'required|exists:screens,id',
            'slot_id'   => 'required|exists:slots,id',
            'movie'     => 'required|string|max:255',
            'day'       => 'required|integer|min:1|max:7',
            'status'    => 'required|in:active,inactive',
        ]);

        // Check schedule duplicate
        if (ScreenSlotAssignment::where('venue_id', $request->venue_id)
            ->where('screen_id', $request->screen_id)
            ->where('slot_id', $request->slot_id)
            ->where('day', $request->day)
            ->exists()
        ) {
            return back()->withErrors(['day' => 'This movie schedule already exists for this screen + slot + day.'])
                ->withInput();
        }

        try {
            ScreenSlotAssignment::create($validated);

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Movie scheduled successfully!');
        } catch (\Exception $e) {
            Log::error('Assignment creation failed: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to create assignment.'])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $assignment = ScreenSlotAssignment::findOrFail($id);

        $venues = Venue::orderBy('name')->get();
        $screens = Screen::where('venue_id', $assignment->venue_id)->get();
        $slots   = Slot::where('venue_id', $assignment->venue_id)->get();

        return view('admin.assignments.edit', compact('assignment', 'venues', 'screens', 'slots'));
    }

    public function update(Request $request, $id)
    {
        $assignment = ScreenSlotAssignment::findOrFail($id);

        $validated = $request->validate([
            'venue_id'  => 'required|exists:venues,id',
            'screen_id' => 'required|exists:screens,id',
            'slot_id'   => 'required|exists:slots,id',
            'movie'     => 'required|string|max:255',
            'day'       => 'required|integer|min:1|max:7',
            'status'    => 'required|in:active,inactive',
        ]);

        // Duplicate check except current record
        if (ScreenSlotAssignment::where('venue_id', $request->venue_id)
            ->where('screen_id', $request->screen_id)
            ->where('slot_id', $request->slot_id)
            ->where('day', $request->day)
            ->where('id', '!=', $assignment->id)
            ->exists()
        ) {
            return back()->withErrors(['day' => 'This movie schedule already exists.'])
                ->withInput();
        }

        try {
            $assignment->update($validated);

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Assignment updated successfully!');
        } catch (\Exception $e) {
            Log::error('Assignment update failed: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to update assignment.'])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $assignment = ScreenSlotAssignment::findOrFail($id);
            $assignment->delete();

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Assignment deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Assignment deletion failed: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to delete assignment.']);
        }
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'screen_id' => 'required|exists:screens,id',
            'day'       => 'required|integer|min:1|max:7',
        ]);

        $screenId = $request->screen_id;
        $day      = $request->day;

        // Slots already assigned for this screen + day
        $assignedSlotIds = ScreenSlotAssignment::where('screen_id', $screenId)
            ->where('day', $day)
            ->pluck('slot_id')
            ->toArray();

        // Return ONLY unassigned slots for this screen
        $slots = Slot::where('venue_id', $request->venue_id)
            ->whereNotIn('id', $assignedSlotIds)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'slots' => $slots
        ]);
    }


    /**
     * AJAX: Fetch Screens + Slots based on Venue
     */


    public function venueData(Request $request)
    {
        $screens = Screen::where('venue_id', $request->venue_id)->get();
        $slots   = Slot::where('venue_id', $request->venue_id)->get();

        return response()->json([
            'screens' => $screens,
            'slots'   => $slots,
        ]);
    }
}
