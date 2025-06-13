<?php

namespace App\Http\Controllers;

use App\Models\RaceCandidate;
use App\Models\Race;
use App\Models\Candidate;
use Illuminate\Http\Request;

class RaceCandidateController extends Controller
{
     /**
     * Display a listing of race-candidate entries.
     */
    public function index()
    {
        // eager‐load race.title and candidate.name
        $entries = RaceCandidate::with(['race:id,title', 'candidate:id,name'])
                    ->latest()
                    ->paginate(10);

        return view('admin.race_candidates.index', compact('entries'));
    }

    /**
     * Show the form for creating a new race‐candidate entry.
     */
    public function create()
    {
        return view('admin.race_candidates.create', [
            'races'      => Race::all(['id', 'title']),
            'candidates' => Candidate::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created race‐candidate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'race_id'           => 'required|exists:races,id',
            'candidate_id'      => 'required|exists:candidates,id',
            'support_percentage'=> 'required|numeric|min:0|max:100',
        ]);

        RaceCandidate::create($validated);

        return redirect()
            ->route('race_candidates.index')
            ->with('success', 'Race‐Candidate entry created successfully.');
    }

    /**
     * Show the form for editing the specified race‐candidate.
     */
    public function edit(RaceCandidate $race_candidate)
    {
        return view('admin.race_candidates.edit', [
            'entry'      => $race_candidate,
            'races'      => Race::all(['id', 'title']),
            'candidates' => Candidate::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified race‐candidate in storage.
     */
    public function update(Request $request, RaceCandidate $race_candidate)
    {
        $validated = $request->validate([
            'race_id'           => 'required|exists:races,id',
            'candidate_id'      => 'required|exists:candidates,id',
            'support_percentage'=> 'required|numeric|min:0|max:100',
        ]);

        $race_candidate->update($validated);

        return redirect()
            ->route('race_candidates.index')
            ->with('success', 'Race‐Candidate entry updated successfully.');
    }

    /**
     * Remove the specified race‐candidate from storage.
     */
    public function destroy(RaceCandidate $race_candidate)
    {
        $race_candidate->delete();

        return redirect()
            ->route('race_candidates.index')
            ->with('success', 'Race‐Candidate entry deleted successfully.');
    }
}
