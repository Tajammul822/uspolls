<?php

namespace App\Http\Controllers;

use App\Models\ElectionPoll;
use App\Models\Poll;
use Illuminate\Http\Request;
use App\Models\ElectionPollResult;
use Illuminate\Support\Arr;

class ElectionPollController extends Controller
{
    public function index(Request $request)
    {
        // 1) Grab & validate the poll_id
        $pollId = $request->query('poll_id');
        abort_unless($pollId && Poll::find($pollId)?->poll_type === 'election', 404);

        // 2) Fetch only those child‐records
        $electionPolls = ElectionPoll::where('poll_id', $pollId)
            ->latest()
            ->get();

        // 3) Pass both the items *and* the parent poll (optional)
        $poll = Poll::findOrFail($pollId);

        return view('admin.election_polls.index', compact('electionPolls', 'poll'));
    }

    public function create(Request $request)
    {
        $poll    = Poll::findOrFail($request->query('poll_id'));

        // Load exactly the candidates on this poll
        $poll->load('candidates');

        return view('admin.election_polls.create', compact('poll'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'poll_id'          => 'required|exists:polls,id',
            'poll_date'        => 'required|date',
            'pollster_source'  => 'required|string',
            'sample_size'      => 'required|integer',
            'candidate_ids'    => 'required|array',
            'candidate_ids.*'  => 'required|distinct|exists:candidates,id',
            'results'          => 'required|array',
            'results.*'        => 'required|numeric|min:0|max:100',
        ]);

        // 1) Create the parent ElectionPoll
        $election = ElectionPoll::create(Arr::only($data, [
            'poll_id',
            'poll_date',
            'pollster_source',
            'sample_size'
        ]));

        // 2) Insert each result
        foreach ($data['candidate_ids'] as $candId) {
            ElectionPollResult::create([
                'election_poll_id' => $election->id,
                'candidate_id'     => $candId,
                'result_percentage' => $data['results'][$candId]
            ]);
        }

        return redirect()
            ->route('election_polls.index', ['poll_id' => $election->poll_id])
            ->with('success', 'Election poll created with results.');
    }

    public function edit(Request $request, ElectionPoll $election_poll)
    {
        $poll = Poll::findOrFail($election_poll->poll_id);

        // Load its candidates
        $poll->load('candidates');

        // Also eager-load existing results so we can prefill
        $election_poll->load('results');

        return view('admin.election_polls.edit', compact('poll', 'election_poll'));
    }

    /**
     * Update the specified ElectionPoll in storage.
     */
    public function update(Request $request, ElectionPoll $election_poll)
    {
        $data = $request->validate([
            'poll_date'        => 'required|date',
            'pollster_source'  => 'required|string',
            'sample_size'      => 'required|integer',
            'candidate_ids'    => 'required|array',
            'candidate_ids.*'  => 'required|distinct|exists:candidates,id',
            'results'          => 'required|array',
            'results.*'        => 'required|numeric|min:0|max:100',
        ]);

        // 1) Update the ElectionPoll
        $election_poll->update(Arr::only($data, [
            'poll_date',
            'pollster_source',
            'sample_size'
        ]));

        // 2) Upsert each result
        foreach ($data['candidate_ids'] as $candId) {
            ElectionPollResult::updateOrCreate(
                [
                    'election_poll_id' => $election_poll->id,
                    'candidate_id'     => $candId
                ],
                ['result_percentage' => $data['results'][$candId]]
            );
        }

        return redirect()
            ->route('election_polls.index', ['poll_id' => $election_poll->poll_id])
            ->with('success', 'Election poll updated with results.');
    }
    /**
     * Remove the specified ElectionPoll from storage.
     */
    public function destroy(ElectionPoll $election_poll)
    {
        $pollId = $election_poll->poll_id;
        $election_poll->delete();

        return redirect()
            ->route('election_polls.index', ['poll_id' => $pollId])
            ->with('success', 'Deleted.');
    }
}
