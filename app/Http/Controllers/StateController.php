<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;

class StateController extends Controller
{
    public function index()
    {
        // Paginate 10 per page (adjust as needed)
        $states = State::latest()->paginate(10);
        return view('admin.states.index', compact('states'));
    }

    /**
     * Show the form for creating a new state.
     */
    public function create()
    {
        return view('admin.states.create');
    }

    /**
     * Store a newly created state in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'abbreviation' => 'required|string|size:2',
        ]);

        State::create($validated);

        return redirect()
            ->route('states.index')
            ->with('success', 'State created successfully.');
    }

    /**
     * Show the form for editing the specified state.
     */
    public function edit(State $state)
    {
        return view('admin.states.edit', compact('state'));
    }

    /**
     * Update the specified state in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'abbreviation' => 'required|string|size:2',
        ]);

        $state->update($validated);

        return redirect()
            ->route('states.index')
            ->with('success', 'State updated successfully.');
    }

    /**
     * Remove the specified state from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();

        return redirect()
            ->route('states.index')
            ->with('success', 'State deleted successfully.');
    }
}
