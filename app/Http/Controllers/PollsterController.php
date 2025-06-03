<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pollster;

class PollsterController extends Controller
{
    /**
     * Display a listing of the pollsters.
     */
    public function index()
    {
        // Paginate 10 per page; adjust as needed
        $pollsters = Pollster::latest()->paginate(10);

        return view('admin.pollsters.index', compact('pollsters'));
    }

    /**
     * Show the form for creating a new pollster.
     */
    public function create()
    {
        return view('admin.pollsters.create');
    }

    /**
     * Store a newly created pollster in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'status'      => 'required|in:0,1',
        ]);

        Pollster::create($validated);

        return redirect()
            ->route('pollsters.index')
            ->with('success', 'Pollster created successfully.');
    }

    /**
     * Show the form for editing the specified pollster.
     */
    public function edit(Pollster $pollster)
    {
        return view('admin.pollsters.edit', compact('pollster'));
    }

    /**
     * Update the specified pollster in storage.
     */
    public function update(Request $request, Pollster $pollster)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'status'      => 'required|in:0,1',
        ]);

        $pollster->update($validated);

        return redirect()
            ->route('pollsters.index')
            ->with('success', 'Pollster updated successfully.');
    }

    /**
     * Remove the specified pollster from storage.
     */
    public function destroy(Pollster $pollster)
    {
        $pollster->delete();

        return redirect()
            ->route('pollsters.index')
            ->with('success', 'Pollster deleted successfully.');
    }
}
