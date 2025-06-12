<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ElectionPollResult;

use App\Models\ElectionPoll;
use App\Models\Candidate;

class ElectionPollResultController extends Controller
{
    public function index(Request $request)
    {
        $election_poll_id = $request->query('election_poll_id');

        if (!$election_poll_id) {
            abort(404, 'Election Poll ID is required.');
        }

        $poll_results = ElectionPollResult::where('election_poll_id', $election_poll_id)->with('candidate')->get();
        $poll = ElectionPoll::findOrFail($election_poll_id);

        return view('admin.election_poll_results.index', compact('poll_results', 'poll'));
    }

    public function create(Request $request)
    {
        $election_poll_id = $request->query('election_poll_id');
        $electionPoll    = ElectionPoll::with('poll.candidates')
            ->findOrFail($election_poll_id);

        // The parent Poll:
        $poll       = $electionPoll->poll;
        // Only the candidates attached to that Poll:
        $candidates = $poll->candidates;

        return view(
            'admin.election_poll_results.create',
            compact('electionPoll', 'poll', 'candidates')
        );
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'election_poll_id' => 'required|exists:election_polls,id',
            'candidate_id' => 'required|exists:candidates,id',
            'result_percentage' => 'required|numeric|min:0|max:100',
        ]);

        ElectionPollResult::create($validated);

        return redirect()->route('election_polls_results.index', ['election_poll_id' => $request->election_poll_id])
            ->with('success', 'Poll result created successfully.');
    }

    public function edit($id)
    {
        $result         = ElectionPollResult::findOrFail($id);
        $electionPoll   = ElectionPoll::with('poll.candidates')
            ->findOrFail($result->election_poll_id);

        $poll       = $electionPoll->poll;
        $candidates = $poll->candidates;

        return view(
            'admin.election_poll_results.edit',
            compact('result', 'electionPoll', 'poll', 'candidates')
        );
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'result_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $result = ElectionPollResult::findOrFail($id);
        $result->update($validated);

        return redirect()->route('election_polls_results.index', ['election_poll_id' => $result->election_poll_id])
            ->with('success', 'Poll result updated successfully.');
    }

    public function destroy($id)
    {
        $result = ElectionPollResult::findOrFail($id);
        $poll_id = $result->election_poll_id;
        $result->delete();

        return redirect()->route('election_polls_results.index', ['election_poll_id' => $poll_id])
            ->with('success', 'Poll result deleted successfully.');
    }
}
