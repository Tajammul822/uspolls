<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;


class CandidateController extends Controller
{
     /**
     * Display a listing of candidates.
     */
    public function index()
    {
        // Paginate 10 per page (adjust as needed)
        $candidates = Candidate::latest()->paginate(10);
        return view('admin.candidates.index', compact('candidates'));
    }

    /**
     * Show the form for creating a new candidate.
     */
    public function create()
    {
        return view('admin.candidates.create');
    }

    /**
     * Store a newly created candidate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'party'  => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        Candidate::create($validated);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate created successfully.');
    }

    /**
     * Show the form for editing the specified candidate.
     */
    public function edit(Candidate $candidate)
    {
        return view('admin.candidates.edit', compact('candidate'));
    }

    /**
     * Update the specified candidate in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'party'  => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $candidate->update($validated);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate updated successfully.');
    }

    /**
     * Remove the specified candidate from storage.
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidate deleted successfully.');
    }
}
