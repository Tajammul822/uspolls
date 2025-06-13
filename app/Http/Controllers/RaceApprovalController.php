<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RaceApproval;
use App\Models\Race;
use Illuminate\Auth\Events\Validated;

class RaceApprovalController extends Controller
{
    public function index(Request $request)
    {
        $raceId = $request->query('race_id');
        abort_unless($raceId && Race::find($raceId)?->race === 'approval', 404);

        $raceApprovals = RaceApproval::where('race_id', $raceId)
                                     ->latest()
                                     ->get();

        $race = Race::findOrFail($raceId);

        return view('admin.race_approvals.index', compact('raceApprovals', 'race'));
    }

    public function create(Request $request)
    {
        // → pull exactly one Race by ?race_id=…
        $race = Race::findOrFail($request->query('race_id'));

        return view('admin.race_approvals.create', compact('race'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'race_id'          => 'required|exists:races,id',
            'name'             => 'required|string',
            'race_date'        => 'required|date',
            'pollster'         => 'required|string',
            'sample_size'      => 'required|integer',
            'approve_rating'   => 'required|numeric|min:0|max:100',
            'disapprove_rating'=> 'required|numeric|min:0|max:100',
        ]);

        RaceApproval::create($validated);

        return redirect()
            ->route('race_approvals.index', ['race_id' => $validated['race_id']])
            ->with('success', 'Race approval created successfully.');
    }

    public function edit(Request $request, RaceApproval $race_approval)
    {

        $approval = $race_approval;

        $race = Race::findOrFail($race_approval->race_id);

        return view('admin.race_approvals.edit', compact('approval', 'race'));
    }

    public function update(Request $request, RaceApproval $race_approval)
    {
        $validated = $request->validate([
            'race_id'          => 'required|exists:races,id',
            'name'             => 'required|string',
            'race_date'        => 'required|date',
            'pollster'         => 'required|string',
            'sample_size'      => 'required|integer',
            'approve_rating'   => 'required|numeric|min:0|max:100',
            'disapprove_rating'=> 'required|numeric|min:0|max:100',
        ]);

        $updated = $race_approval->update($validated);
        return redirect()
            ->route('race_approvals.index', ['race_id' => $validated['race_id']])
            ->with('success', 'Race approval updated successfully.');
    }

    public function destroy(RaceApproval $race_approval)
    {
        $raceId = $race_approval->race_id;
        $race_approval->delete();

        return redirect()
            ->route('race_approvals.index', ['race_id' => $raceId])
            ->with('success', 'Race approval deleted successfully.');
    }
}
