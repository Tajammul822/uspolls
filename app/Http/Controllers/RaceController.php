<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Race;
class RaceController extends Controller
{
    /**
     * Display a listing of races.
     */
    public function index()
    {
        // Paginate 10 per page (adjust as needed)
        $races = Race::latest('id')->paginate(10);
        return view('admin.races.index', compact('races'));
    }

    /**
     * Show the form for creating a new race.
     */
    public function create()
    {
        return view('admin.races.create');
    }

    /**
     * Store a newly created race in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        Race::create($validated);

        return redirect()
            ->route('races.index')
            ->with('success', 'Race created successfully.');
    }

    /**
     * Show the form for editing the specified race.
     */
    public function edit(Race $race)
    {
        return view('admin.races.edit', compact('race'));
    }

    /**
     * Update the specified race in storage.
     */
    public function update(Request $request, Race $race)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $race->update($validated);

        return redirect()
            ->route('races.index')
            ->with('success', 'Race updated successfully.');
    }

    /**
     * Remove the specified race from storage.
     */
    public function destroy(Race $race)
    {
        $race->delete();

        return redirect()
            ->route('races.index')
            ->with('success', 'Race deleted successfully.');
    }
}
