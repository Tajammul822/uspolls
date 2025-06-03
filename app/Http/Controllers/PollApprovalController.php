<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PollApproval;
use App\Models\Poll;

class PollApprovalController extends Controller
{
    /**
     * Display a listing of poll approvals.
     */
    public function index()
    {
        // Eager‑load the poll’s title
        $approvals = PollApproval::with(['poll:id,title'])
                     ->latest()
                     ->paginate(10);

        return view('admin.poll_approvals.index', compact('approvals'));
    }

    /**
     * Show the form for creating a new poll approval.
     */
    public function create()
    {
        return view('admin.poll_approvals.create', [
            'polls' => Poll::all(['id', 'title']),
        ]);
    }

    /**
     * Store a newly created poll approval in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'poll_id'              => 'required|exists:polls,id',
            'approve_percentage'   => 'required|numeric|min:0|max:100',
            'disapprove_percentage'=> 'required|numeric|min:0|max:100',
            'neutral_percentage'   => 'nullable|numeric|min:0|max:100',
            'subject'              => 'required|string|max:255',
        ]);

        PollApproval::create($validated);

        return redirect()
            ->route('poll_approvals.index')
            ->with('success', 'Poll‑Approval entry created successfully.');
    }

    /**
     * Show the form for editing the specified poll approval.
     */
    public function edit(PollApproval $poll_approval)
    {
        return view('admin.poll_approvals.edit', [
            'approval' => $poll_approval,
            'polls'    => Poll::all(['id', 'title']),
        ]);
    }

    /**
     * Update the specified poll approval in storage.
     */
    public function update(Request $request, PollApproval $poll_approval)
    {
        $validated = $request->validate([
            'poll_id'              => 'required|exists:polls,id',
            'approve_percentage'   => 'required|numeric|min:0|max:100',
            'disapprove_percentage'=> 'required|numeric|min:0|max:100',
            'neutral_percentage'   => 'nullable|numeric|min:0|max:100',
            'subject'              => 'required|string|max:255',
        ]);

        $poll_approval->update($validated);

        return redirect()
            ->route('poll_approvals.index')
            ->with('success', 'Poll‑Approval entry updated successfully.');
    }

    /**
     * Remove the specified poll approval from storage.
     */
    public function destroy(PollApproval $poll_approval)
    {
        $poll_approval->delete();

        return redirect()
            ->route('poll_approvals.index')
            ->with('success', 'Poll‑Approval entry deleted successfully.');
    }
}
