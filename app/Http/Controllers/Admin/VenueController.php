<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;   
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::orderBy('name')->get();
        return view('admin.venues.index', compact('venues'));
    }

    public function create()
    {
        return view('admin.venues.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:venues,name',
            'location' => 'nullable|string|max:255',
        ]);

        try {
            Venue::create($validated);

            return redirect()->route('admin.venues.index')
                ->with('success', 'Venue created successfully!');
        } catch (\Exception $e) {
            Log::error('Venue creation failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'Failed to create venue.'])
                         ->withInput();
        }
    }

    public function edit($id)
    {
        $venue = Venue::findOrFail($id);

        return view('admin.venues.edit', compact('venue'));
    }

    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:venues,name,' . $venue->id,
            'location' => 'nullable|string|max:255',
        ]);

        try {
            $venue->update($validated);

            return redirect()->route('admin.venues.index')
                ->with('success', 'Venue updated successfully!');
        } catch (\Exception $e) {
            Log::error('Venue update failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'Failed to update venue.'])
                         ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $venue = Venue::findOrFail($id);
            $venue->delete();

            return redirect()->route('admin.venues.index')
                ->with('success', 'Venue deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Venue deletion failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'Failed to delete venue.']);
        }
    }
}
