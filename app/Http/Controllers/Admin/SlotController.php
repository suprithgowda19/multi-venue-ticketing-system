<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slot;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SlotController extends Controller
{
    public function index()
    {
        $slots = Slot::with('venue')
            ->orderBy('venue_id')
            ->orderBy('start_time')
            ->get();

        return view('admin.slots.index', compact('slots'));
    }

    public function create()
    {
        $venues = Venue::orderBy('name')->get();
        return view('admin.slots.create', compact('venues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venue_id'   => 'required|exists:venues,id',
            'start_time' => 'required|date_format:H:i',
        ]);

        // Check duplicate time per venue
        if (Slot::where('venue_id', $request->venue_id)
                ->where('start_time', $request->start_time)
                ->exists()) 
        {
            return back()->withErrors(['start_time' => 'This slot time already exists for this venue.'])
                         ->withInput();
        }

        try {
            Slot::create($validated);

            return redirect()->route('admin.slots.index')
                ->with('success', 'Slot created successfully!');
        } catch (\Exception $e) {
            Log::error('Slot creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create slot.'])->withInput();
        }
    }

    public function edit($id)
    {
        $slot = Slot::findOrFail($id);
        $venues = Venue::orderBy('name')->get();

        return view('admin.slots.edit', compact('slot', 'venues'));
    }

    public function update(Request $request, $id)
    {
        $slot = Slot::findOrFail($id);

        $validated = $request->validate([
            'venue_id'   => 'required|exists:venues,id',
            'start_time' => 'required|date_format:H:i',
        ]);

        // Unique check (ignore current)
        if (Slot::where('venue_id', $request->venue_id)
                ->where('start_time', $request->start_time)
                ->where('id', '!=', $slot->id)
                ->exists()) 
        {
            return back()->withErrors(['start_time' => 'This slot time already exists for this venue.'])
                         ->withInput();
        }

        try {
            $slot->update($validated);

            return redirect()->route('admin.slots.index')
                ->with('success', 'Slot updated successfully!');
        } catch (\Exception $e) {
            Log::error('Slot update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update slot.'])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            Slot::findOrFail($id)->delete();

            return redirect()->route('admin.slots.index')
                ->with('success', 'Slot deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Slot deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete slot.']);
        }
    }
}
