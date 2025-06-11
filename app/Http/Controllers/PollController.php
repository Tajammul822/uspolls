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
            // 1) grab all incoming IDs (filter out blanks)
            $incoming = collect($request->input('candidates', []))
                ->pluck('candidate_id')
                ->filter()
                ->unique()
                ->toArray();

            // 2) delete only those *not* in incoming
            PollCandidate::where('poll_id', $poll->id)
                ->whereNotIn('candidate_id', $incoming)
                ->delete();

            // 3) upsert the rest
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


    public function details(Poll $poll)
    {
        // eager‐load everything you need
        $poll->load(
            'pollCandidates.candidate',
            'pollApproval',
            'electionPoll.results.candidate'
        );

        // prepare your detail‐view variables
        $approval = $poll->pollApproval;
        $election = $poll->electionPoll;
        $results  = $election
            ? $election->results
            : collect();

        return view('admin.polls.details', compact(
            'poll',
            'approval',
            'election',
            'results'
        ));
    }


    public function storeApproval(Request $request)
    {
        $data = $request->validate([
            'poll_id'           => 'required|exists:polls,id',
            'name'              => 'required|string',
            'poll_date'         => 'required|date',
            'pollster'          => 'required|string',
            'sample_size'       => 'required|integer',
            'approve_rating'    => 'required|numeric|min:0|max:100',
            'disapprove_rating' => 'required|numeric|min:0|max:100',
        ]);

        // Remove poll_id before passing to updateOrCreate
        $payload = Arr::except($data, ['poll_id']);

        PollApproval::updateOrCreate(
            ['poll_id' => $data['poll_id']],
            $payload
        );

        return redirect()->route('polls.index')->with('success', 'Approval poll data saved.');
    }

    public function storeElection(Request $request)
    {
        $data = $request->validate([
            'poll_id'               => 'required|exists:polls,id',
            'poll_date'             => 'required|date',
            'pollster_source'       => 'required|string',
            'sample_size'           => 'required|integer',
            'results.*.candidate_id' => 'required|exists:candidates,id',
            'results.*.result_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Upsert the parent election_poll row
        $election = ElectionPoll::updateOrCreate(
            // match on poll_id
            ['poll_id' => $data['poll_id']],
            // now *also* assign poll_id on insert
            [
                'poll_id'          => $data['poll_id'],
                'poll_date'        => $data['poll_date'],
                'pollster_source'  => $data['pollster_source'],
                'sample_size'      => $data['sample_size'],
            ]
        );

        // Sync each candidate result
        foreach ($data['results'] as $result) {
            ElectionPollResult::updateOrCreate(
                [
                    'election_poll_id' => $election->id,
                    'candidate_id'     => $result['candidate_id'],
                ],
                ['result_percentage' => $result['result_percentage']]
            );
        }

        return redirect()->route('polls.index')->with('success', 'Election results saved.');
    }
}
