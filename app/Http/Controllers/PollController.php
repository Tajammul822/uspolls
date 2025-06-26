<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Race;
use Illuminate\Http\Request;
use App\Models\PollResult;
use App\Models\Pollster;
use Illuminate\Support\Arr;

class PollController extends Controller
{
    public function index(Request $request)
    {
        // 1) Grab & validate the race_id
        $raceId = $request->query('race_id');
        abort_unless($raceId && Race::find($raceId)?->race === 'election', 404);

        // 2) Fetch only those child‐records
        $polls = Poll::where('race_id', $raceId)
            ->latest()
            ->get();

        // 3) Pass both the items *and* the parent poll (optional)
        $race = Race::findOrFail($raceId);

        return view('admin.polls.index', compact('polls', 'race'));
    }

    public function create(Request $request)
    {
        $race    = Race::findOrFail($request->query('race_id'));

        // Load exactly the candidates on this race
        $race->load('candidates');

        $pollsters = Pollster::all();

        return view('admin.polls.create', compact('race', 'pollsters'));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'race_id'          => 'required|exists:races,id',
            'poll_date'        => 'required|date',
            'pollster_id'      => 'required|exists:pollsters,id',
            'sample_size'      => 'required|integer',
            'candidate_ids'    => 'required|array',
            'candidate_ids.*'  => 'required|distinct|exists:candidates,id',
            'results'          => 'required|array',
            'results.*'        => 'required|numeric|min:0|max:100',
        ]);

       
        $poll = Poll::create(Arr::only($data, [
            'race_id',
            'poll_date',
            'pollster_id',
            'sample_size',
        ]));

        // 2) Insert each result
        foreach ($data['candidate_ids'] as $candId) {
            PollResult::create([
                'poll_id' => $poll->id,
                'candidate_id'     => $candId,
                'result_percentage' => $data['results'][$candId]
            ]);
        }

        return redirect()
            ->route('polls.index', ['race_id' => $poll->race_id])
            ->with('success', 'Poll created with results.');
    }

    public function edit(Request $request, Poll $poll)
    {
        $race = Race::findOrFail($poll->race_id);

        // Load its candidates
        $race->load('candidates');

        // Also eager-load existing results so we can prefill
        $poll->load('results');

        $pollsters = Pollster::all();

        return view('admin.polls.edit', compact('race', 'poll', 'pollsters'));
    }

    /**
     * Update the specified Poll in storage.
     */
    public function update(Request $request, Poll $poll)
    {
        $data = $request->validate([
            'poll_date'        => 'required|date',
            'pollster_id'      => 'required|exists:pollsters,id',
            'sample_size'      => 'required|integer',
            'candidate_ids'    => 'required|array',
            'candidate_ids.*'  => 'required|distinct|exists:candidates,id',
            'results'          => 'required|array',
            'results.*'        => 'required|numeric|min:0|max:100',
        ]);

        // 1) Update the Poll
        $poll->update(Arr::only($data, [
            'poll_date',
            'pollster_id',
            'sample_size',
        ]));

        // 2) Upsert each result
        foreach ($data['candidate_ids'] as $candId) {
            PollResult::updateOrCreate(
                [
                    'poll_id' => $poll->id,
                    'candidate_id'     => $candId
                ],
                ['result_percentage' => $data['results'][$candId]]
            );
        }

        return redirect()
            ->route('polls.index', ['race_id' => $poll->race_id])
            ->with('success', 'Poll updated with results.');
    }
    /**
     * Remove the specified Poll from storage.
     */
    public function destroy(Poll $poll)
    {
        $raceId = $poll->race_id;
        $poll->delete();

        return redirect()
            ->route('polls.index', ['race_id' => $raceId])
            ->with('success', 'Deleted.');
    }
}
