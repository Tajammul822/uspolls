<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\Poll;
// use App\Models\Race;
// use App\Models\Pollster;
use App\Models\State;

class PollController extends Controller
{
    /**
     * Display a listing of polls.
     */
    public function index()
    {
        // Eager‑load state for display.
        $polls = Poll::with('state')->latest()->paginate(10);

        return view('admin.polls.index', compact('polls'));
    }

    /**
     * Show the form for creating a new poll.
     */
    public function create()
    {
        // We still need the list of states to populate "state_id"
        $states = State::orderBy('name')->get();

        return view('admin.polls.create', compact('states'));
    }

    /**
     * Store a newly created poll in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_name'     => 'required|string|max:255',
            'party'              => 'required|string|max:255',
            'race'               => 'required|in:primary,general,midterm,approval',
            'support_percentage' => 'required|numeric|min:0|max:100',
            'approval_rating'    => 'required|in:Approve,Disapprove,Neutral',
            'pollster'           => 'required|string|max:255',
            'state_id'           => 'nullable|exists:states,id',
            'field_date_start'   => 'required|date',
            'field_date_end'     => 'required|date|after_or_equal:field_date_start',
            'release_date'       => 'required|date',
            'sample_size'        => 'required|integer|min:1',
            'margin_of_error'    => 'required|numeric|min:0',
            'source_url'         => 'required|url',
            'tags'               => 'nullable|string',
            'status'             => 'required|in:0,1',
        ]);

        Poll::create([
            'candidate_name'     => $validated['candidate_name'],
            'party'              => $validated['party'],
            'race'               => $validated['race'],
            'support_percentage' => $validated['support_percentage'],
            'approval_rating'    => $validated['approval_rating'],
            'pollster'           => $validated['pollster'],
            'state_id'           => $validated['state_id'],
            'field_date_start'   => $validated['field_date_start'],
            'field_date_end'     => $validated['field_date_end'],
            'release_date'       => $validated['release_date'],
            'sample_size'        => $validated['sample_size'],
            'margin_of_error'    => $validated['margin_of_error'],
            'source_url'         => $validated['source_url'],
            'tags'               => $validated['tags'] ?? '',
            'status'             => $validated['status'],
        ]);

        return redirect()
            ->route('polls.index')
            ->with('success', 'Poll created successfully.');
    }

    /**
     * Show the form for editing the specified poll.
     */
    public function edit(Poll $poll)
    {
        // List of states to repopulate the dropdown
        $states = State::orderBy('name')->get();

        return view('admin.polls.edit', compact('poll', 'states'));
    }

    /**
     * Update the specified poll in storage.
     */
    public function update(Request $request, Poll $poll)
    {
        $validated = $request->validate([
            'candidate_name'     => 'required|string|max:255',
            'party'              => 'required|string|max:255',
            'race'               => 'required|in:primary,general,midterm,approval',
            'support_percentage' => 'required|numeric|min:0|max:100',
            'approval_rating'    => 'required|in:Approve,Disapprove,Neutral',
            'pollster'           => 'required|string|max:255',
            'state_id'           => 'nullable|exists:states,id',
            'field_date_start'   => 'required|date',
            'field_date_end'     => 'required|date|after_or_equal:field_date_start',
            'release_date'       => 'required|date',
            'sample_size'        => 'required|integer|min:1',
            'margin_of_error'    => 'required|numeric|min:0',
            'source_url'         => 'required|url',
            'tags'               => 'nullable|string',
            'status'             => 'required|in:0,1',
        ]);

        $poll->update([
            'candidate_name'     => $validated['candidate_name'],
            'party'              => $validated['party'],
            'race'               => $validated['race'],
            'support_percentage' => $validated['support_percentage'],
            'approval_rating'    => $validated['approval_rating'],
            'pollster'           => $validated['pollster'],
            'state_id'           => $validated['state_id'],
            'field_date_start'   => $validated['field_date_start'],
            'field_date_end'     => $validated['field_date_end'],
            'release_date'       => $validated['release_date'],
            'sample_size'        => $validated['sample_size'],
            'margin_of_error'    => $validated['margin_of_error'],
            'source_url'         => $validated['source_url'],
            'tags'               => $validated['tags'] ?? '',
            'status'             => $validated['status'],
        ]);

        return redirect()
            ->route('polls.index')
            ->with('success', 'Poll updated successfully.');
    }

    /**
     * Remove the specified poll from storage.
     */
    public function destroy(Poll $poll)
    {
        $poll->delete();

        return redirect()
            ->route('polls.index')
            ->with('success', 'Poll deleted successfully.');
    }





    // old 
    // public function index()
    // {
    //     $polls = Poll::latest()->paginate(10);
    //     return view('admin.polls.index', compact('polls'));
    // }

    // public function create()
    // {
    //     return view('admin.polls.create', [
    //         'races' => Race::all(),
    //         'pollsters' => Pollster::all(),
    //         'states' => State::all(),
    //     ]);
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'race_id' => 'required|exists:races,id',
    //         'title' => 'required|string|max:255',
    //         'pollster_id' => 'required|exists:pollsters,id',
    //         'state_id' => 'nullable|exists:states,id',
    //         'field_date_start' => 'required|date',
    //         'field_date_end' => 'required|date',
    //         'release_date' => 'required|date',
    //         'sample_size' => 'required|integer',
    //         'margin_of_error' => 'required|numeric',
    //         'source_url' => 'required|url',
    //         'tags' => 'nullable|string',
    //         'status' => 'required|integer',
    //     ]);

    //     Poll::create($validated);

    //     return redirect()->route('polls.index')->with('success', 'Poll created successfully.');
    // }

    // public function show(Poll $poll)
    // {
    //     return view('admin.polls.show', compact('poll'));
    // }

    // public function edit(Poll $poll)
    // {
    //     return view('admin.polls.edit', [
    //         'poll' => $poll,
    //         'races' => Race::all(),
    //         'pollsters' => Pollster::all(),
    //         'states' => State::all(),
    //     ]);
    // }

    // public function update(Request $request, Poll $poll)
    // {
    //     $validated = $request->validate([
    //         'race_id' => 'required|exists:races,id',
    //         'title' => 'required|string|max:255',
    //         'pollster_id' => 'required|exists:pollsters,id',
    //         'state_id' => 'nullable|exists:states,id',
    //         'field_date_start' => 'required|date',
    //         'field_date_end' => 'required|date',
    //         'release_date' => 'required|date',
    //         'sample_size' => 'required|integer',
    //         'margin_of_error' => 'required|numeric',
    //         'source_url' => 'required|url',
    //         'tags' => 'nullable|string',
    //         'status' => 'required|integer',
    //     ]);

    //     $poll->update($validated);

    //     return redirect()->route('polls.index')->with('success', 'Poll updated successfully.');
    // }

    // public function destroy(Poll $poll)
    // {
    //     $poll->delete();
    //     return redirect()->route('polls.index')->with('success', 'Poll deleted successfully.');
    // }
}
