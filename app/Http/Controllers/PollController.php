<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Candidate;
use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\PollCandidate;

use App\Models\PollApproval;
use App\Models\ElectionPoll;
use App\Models\ElectionPollResult;
use Illuminate\Support\Arr;
// use App\Models\Race;
// use App\Models\Pollster;
use App\Models\State;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::all();
        $states = State::all();
        return view('admin.polls.index', compact('polls', 'states'));
    }

    public function create()
    {
        $candidates = Candidate::all();
        $approvalCandidate = null;
        $states = State::all();
        return view('admin.polls.create',  compact('candidates', 'approvalCandidate', 'states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'poll_type' => 'required|in:election,approval',
            'race_type' => 'nullable|in:president,senate,house,governor,other',
            'state_id' => 'nullable|exists:states,id',
            'election_round' => 'nullable|in:primary,general',
        ]);

        $poll = Poll::create($validated);

        $validated2 = $request->validate([
            'candidates.*.candidate_id' => 'nullable|exists:candidates,id',
            'candidate_id' => 'nullable|exists:candidates,id',
        ]);

        // Handle election type with multiple candidates
        if ($request->poll_type === 'election') {
            foreach ($request->input('candidates', []) as $cd) {
                if (!empty($cd['candidate_id'])) {
                    PollCandidate::create([
                        'poll_id' => $poll->id,
                        'candidate_id' => $cd['candidate_id']
                    ]);
                }
            }
        }

        // Handle approval type with single candidate
        if ($request->poll_type === 'approval' && !empty($validated2['candidate_id'])) {
            PollCandidate::create([
                'poll_id' => $poll->id,
                'candidate_id' => $validated2['candidate_id']
            ]);
        }

        return redirect()->route('polls.index')->with('success', 'Poll created successfully');
    }

    public function edit(Poll $poll)
    {
        $candidates = Candidate::all();
        $states = State::all();
        // Build an array of ['candidate_id'=>…, 'party'=>…] for the form
        $rows = $poll->pollCandidates()->with('candidate')->get()
            ->map(fn($pc) => [
                'candidate_id' => $pc->candidate_id,
                'party'        => $pc->candidate->party,
            ])
            ->toArray();

        $poll->candidates = $rows;

        $approvalCandidate = $poll->pollCandidates()->first();

        return view(
            'admin.polls.edit',
            compact('poll', 'candidates', 'approvalCandidate', 'states')
        );
    }

    public function update(Request $request, Poll $poll)
    {
        $validated = $request->validate([
            'poll_type'      => 'required|in:election,approval',
            'race_type'      => 'nullable|in:president,senate,house,governor,other',
            'state_id'      => 'nullable|exists:states,id',
            'election_round' => 'nullable|in:primary,general',
        ]);
        $poll->update($validated);

        // Re-validate the candidate inputs
        $request->validate([
            'candidates.*.candidate_id' => 'nullable|exists:candidates,id',
            'candidate_id'              => 'nullable|exists:candidates,id',
        ]);

        if ($request->poll_type === 'election') {
            // incoming IDs
            $incoming = collect($request->input('candidates', []))
                ->pluck('candidate_id')
                ->filter()
                ->unique()
                ->toArray();

            // existing IDs
            $existing = $poll->pollCandidates()->pluck('candidate_id')->toArray();

            // which were removed?
            $removed = array_diff($existing, $incoming);

            if ($removed) {
                // delete pivot rows
                PollCandidate::where('poll_id', $poll->id)
                    ->whereIn('candidate_id', $removed)
                    ->delete();

                // delete any results for those candidate_ids
                ElectionPollResult::whereIn('candidate_id', $removed)->delete();
            }

            // re-add the rest
            foreach ($incoming as $cid) {
                PollCandidate::firstOrCreate([
                    'poll_id'      => $poll->id,
                    'candidate_id' => $cid,
                ]);
            }
        }
        // approval stays untouched
        if ($request->poll_type === 'approval' && $request->filled('candidate_id')) {
            PollCandidate::updateOrCreate(
                ['poll_id' => $poll->id],
                ['candidate_id' => $request->input('candidate_id')]
            );
        }

        return redirect()->route('polls.index')
            ->with('success', 'Poll updated successfully');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();
        return redirect()->route('polls.index')->with('success', 'Poll deleted successfully');
    }
}
