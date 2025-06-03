<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\Race;
use App\Models\Pollster;
use App\Models\State;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::latest()->paginate(10);
        return view('admin.polls.index', compact('polls'));
    }

    public function create()
    {
        return view('admin.polls.create', [
            'races' => Race::all(),
            'pollsters' => Pollster::all(),
            'states' => State::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'race_id' => 'required|exists:races,id',
            'title' => 'required|string|max:255',
            'pollster_id' => 'required|exists:pollsters,id',
            'state_id' => 'nullable|exists:states,id',
            'field_date_start' => 'required|date',
            'field_date_end' => 'required|date',
            'release_date' => 'required|date',
            'sample_size' => 'required|integer',
            'margin_of_error' => 'required|numeric',
            'source_url' => 'required|url',
            'tags' => 'nullable|string',
            'status' => 'required|integer',
        ]);

        Poll::create($validated);

        return redirect()->route('polls.index')->with('success', 'Poll created successfully.');
    }

    public function show(Poll $poll)
    {
        return view('admin.polls.show', compact('poll'));
    }

    public function edit(Poll $poll)
    {
        return view('admin.polls.edit', [
            'poll' => $poll,
            'races' => Race::all(),
            'pollsters' => Pollster::all(),
            'states' => State::all(),
        ]);
    }

    public function update(Request $request, Poll $poll)
    {
        $validated = $request->validate([
            'race_id' => 'required|exists:races,id',
            'title' => 'required|string|max:255',
            'pollster_id' => 'required|exists:pollsters,id',
            'state_id' => 'nullable|exists:states,id',
            'field_date_start' => 'required|date',
            'field_date_end' => 'required|date',
            'release_date' => 'required|date',
            'sample_size' => 'required|integer',
            'margin_of_error' => 'required|numeric',
            'source_url' => 'required|url',
            'tags' => 'nullable|string',
            'status' => 'required|integer',
        ]);

        $poll->update($validated);

        return redirect()->route('polls.index')->with('success', 'Poll updated successfully.');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();
        return redirect()->route('polls.index')->with('success', 'Poll deleted successfully.');
    }
}
