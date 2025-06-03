<?php

namespace App\Http\Controllers;

use App\Models\PollCandidate;
use App\Models\Poll;
use App\Models\Candidate;
use Illuminate\Http\Request;

class PollCandidateController extends Controller
{
     /**
     * Display a listing of poll-candidate entries.
     */
    public function index()
    {
        // eager‐load poll.title and candidate.name
        $entries = PollCandidate::with(['poll:id,title', 'candidate:id,name'])
                    ->latest()
                    ->paginate(10);

        return view('admin.poll_candidates.index', compact('entries'));
    }

    /**
     * Show the form for creating a new poll‐candidate entry.
     */
    public function create()
    {
        return view('admin.poll_candidates.create', [
            'polls'      => Poll::all(['id', 'title']),
            'candidates' => Candidate::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created poll‐candidate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'poll_id'           => 'required|exists:polls,id',
            'candidate_id'      => 'required|exists:candidates,id',
            'support_percentage'=> 'required|numeric|min:0|max:100',
        ]);

        PollCandidate::create($validated);

        return redirect()
            ->route('poll_candidates.index')
            ->with('success', 'Poll‐Candidate entry created successfully.');
    }

    /**
     * Show the form for editing the specified poll‐candidate.
     */
    public function edit(PollCandidate $poll_candidate)
    {
        return view('admin.poll_candidates.edit', [
            'entry'      => $poll_candidate,
            'polls'      => Poll::all(['id', 'title']),
            'candidates' => Candidate::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified poll‐candidate in storage.
     */
    public function update(Request $request, PollCandidate $poll_candidate)
    {
        $validated = $request->validate([
            'poll_id'           => 'required|exists:polls,id',
            'candidate_id'      => 'required|exists:candidates,id',
            'support_percentage'=> 'required|numeric|min:0|max:100',
        ]);

        $poll_candidate->update($validated);

        return redirect()
            ->route('poll_candidates.index')
            ->with('success', 'Poll‐Candidate entry updated successfully.');
    }

    /**
     * Remove the specified poll‐candidate from storage.
     */
    public function destroy(PollCandidate $poll_candidate)
    {
        $poll_candidate->delete();

        return redirect()
            ->route('poll_candidates.index')
            ->with('success', 'Poll‐Candidate entry deleted successfully.');
    }
}
