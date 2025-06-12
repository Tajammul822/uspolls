<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PollApproval;
use App\Models\Poll;
use Illuminate\Auth\Events\Validated;

class PollApprovalController extends Controller
{
    public function index(Request $request)
    {
        $pollId = $request->query('poll_id');
        abort_unless($pollId && Poll::find($pollId)?->poll_type === 'approval', 404);

        $pollApprovals = PollApproval::where('poll_id', $pollId)
                                     ->latest()
                                     ->get();

        $poll = Poll::findOrFail($pollId);

        return view('admin.poll_approvals.index', compact('pollApprovals', 'poll'));
    }

    public function create(Request $request)
    {
        // → pull exactly one Poll by ?poll_id=…
        $poll = Poll::findOrFail($request->query('poll_id'));

        return view('admin.poll_approvals.create', compact('poll'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'poll_id'          => 'required|exists:polls,id',
            'name'             => 'required|string',
            'poll_date'        => 'required|date',
            'pollster'         => 'required|string',
            'sample_size'      => 'required|integer',
            'approve_rating'   => 'required|numeric|min:0|max:100',
            'disapprove_rating'=> 'required|numeric|min:0|max:100',
        ]);

        PollApproval::create($validated);

        return redirect()
            ->route('poll_approvals.index', ['poll_id' => $validated['poll_id']])
            ->with('success', 'Poll approval created successfully.');
    }

    public function edit(Request $request, PollApproval $poll_approval)
    {

        $approval = $poll_approval;
        
        $poll = Poll::findOrFail($poll_approval->poll_id);

        return view('admin.poll_approvals.edit', compact('approval', 'poll'));
    }

    public function update(Request $request, PollApproval $poll_approval)
    {
        $validated = $request->validate([
            'poll_id'          => 'required|exists:polls,id',
            'name'             => 'required|string',
            'poll_date'        => 'required|date',
            'pollster'         => 'required|string',
            'sample_size'      => 'required|integer',
            'approve_rating'   => 'required|numeric|min:0|max:100',
            'disapprove_rating'=> 'required|numeric|min:0|max:100',
        ]);

        $updated = $poll_approval->update($validated);
        return redirect()
            ->route('poll_approvals.index', ['poll_id' => $validated['poll_id']])
            ->with('success', 'Poll approval updated successfully.');
    }

    public function destroy(PollApproval $poll_approval)
    {
        $pollId = $poll_approval->poll_id;
        $poll_approval->delete();

        return redirect()
            ->route('poll_approvals.index', ['poll_id' => $pollId])
            ->with('success', 'Poll approval deleted successfully.');
    }
}
