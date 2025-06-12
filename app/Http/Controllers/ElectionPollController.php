<?php

namespace App\Http\Controllers;

use App\Models\ElectionPoll;
use App\Models\Poll;
use Illuminate\Http\Request;

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
        // just pass the poll so you can show its title, etc.
        $poll = Poll::findOrFail($request->query('poll_id'));
        return view('admin.election_polls.create', compact('poll'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'poll_id'          => 'required|exists:polls,id',
            'poll_date'        => 'required|date',
            'pollster_source'  => 'required|string',
            'sample_size'      => 'required|integer',
        ]);

        ElectionPoll::create($data);

        return redirect()->route('election_polls.index', ['poll_id' => $data['poll_id']])->with('success', 'Item created.');
    }
    public function edit(Request $request, ElectionPoll $election_poll)
    {
        // Fetch the parent poll so we can show its title, etc.
        // We’ll trust that $election_poll->poll_id is valid.
        $poll = Poll::findOrFail($election_poll->poll_id);

        return view('admin.election_polls.edit', compact('election_poll', 'poll'));
    }

    /**
     * Update the specified ElectionPoll in storage.
     */
    public function update(Request $request, ElectionPoll $election_poll)
    {
        $data = $request->validate([
            'poll_date'       => 'required|date',
            'pollster_source' => 'required|string',
            'sample_size'     => 'required|integer',
            // no need to validate poll_id—it won’t change on edit
        ]);

        $election_poll->update($data);

        return redirect()
            ->route('election_polls.index', ['poll_id' => $election_poll->poll_id])
            ->with('success', 'Poll entry updated.');
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
