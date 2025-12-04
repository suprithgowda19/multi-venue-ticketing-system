<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScreenController extends Controller
{
    public function index()
    {
        $screens = Screen::with('venue')->orderBy('venue_id')->orderBy('name')->get();

        return view('admin.screens.index', compact('screens'));
    }

    public function create()
    {
        $venues = Venue::orderBy('name')->get();

        return view('admin.screens.create', compact('venues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'name'     => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        // screen name must be unique per venue
        $exists = Screen::where('venue_id', $request->venue_id)
                        ->where('name', $request->name)
                        ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A screen with this name already exists in this venue.'])
                         ->withInput();
        }

        try {
            Screen::create($validated);

            return redirect()->route('admin.screens.index')
                ->with('success', 'Screen created successfully!');
        } catch (\Exception $e) {
            Log::error('Screen creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create screen.'])->withInput();
        }
    }

    public function edit($id)
    {
        $screen  = Screen::findOrFail($id);
        $venues = Venue::orderBy('name')->get();

        return view('admin.screens.edit', compact('screen', 'venues'));
    }

    public function update(Request $request, $id)
    {
        $screen = Screen::findOrFail($id);

        $validated = $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'name'     => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        // ensure unique name inside venue
        $exists = Screen::where('venue_id', $request->venue_id)
                        ->where('name', $request->name)
                        ->where('id', '!=', $screen->id)
                        ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A screen with this name already exists in this venue.'])
                         ->withInput();
        }

        try {
            $screen->update($validated);

            return redirect()->route('admin.screens.index')
                ->with('success', 'Screen updated successfully!');
        } catch (\Exception $e) {
            Log::error('Screen update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update screen.'])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $screen = Screen::findOrFail($id);
            $screen->delete();

            return redirect()->route('admin.screens.index')
                ->with('success', 'Screen deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Screen deletion failed: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to delete screen.']);
        }
    }
}
