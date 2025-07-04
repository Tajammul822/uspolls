<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use App\Models\Race;
use App\Models\RaceCandidate;
use App\Models\PollResult;
use App\Models\Candidate;
use App\Models\State;

use Illuminate\Support\Arr;

class RaceController extends Controller
{
  public function index()
    {
        // Eager‑load state and pivot rows + their Candidate
        $races = Race::with([
            'state',
            'raceCandidates.candidate'   // load the Candidate for each pivot
        ])->get();

        $states = State::all();
        return view('admin.races.index', compact('races', 'states'));
    }

    public function create()
    {
        $candidates = Candidate::all();
        $approvalCandidate = null;
        $states = State::all();
        return view('admin.races.create',  compact('candidates', 'approvalCandidate', 'states'));
    }

    public function store(Request $request)
    {
        if ($request->input('state_id') === 'general') {
            $request->merge(['state_id' => null]);
        }

        $request->merge([
            'is_featured' => $request->has('is_featured') ? 1 : 0,
        ]);


        $validated = $request->validate([
            'race' => 'required|in:election,approval',
            'race_type' => 'nullable|in:president,senate,house,governor,other',
            'state_id' => 'nullable|exists:states,id',
            'district' => 'nullable|integer',
            'is_featured'      => 'nullable|integer|between:0,1',
            'election_round' => 'nullable|in:primary,general',
        ]);

        $race = Race::create($validated);

        $validated2 = $request->validate([
            'candidates.*.candidate_id' => 'nullable|exists:candidates,id',
            'candidate_id' => 'nullable|exists:candidates,id',
        ]);

        // Handle election type with multiple candidates
        if ($request->race === 'election') {
            foreach ($request->input('candidates', []) as $cd) {
                if (!empty($cd['candidate_id'])) {
                    RaceCandidate::create([
                        'race_id' => $race->id,
                        'candidate_id' => $cd['candidate_id']
                    ]);
                }
            }
        }

        // Handle approval type with single candidate
        if ($request->race === 'approval' && !empty($validated2['candidate_id'])) {
            RaceCandidate::create([
                'race_id' => $race->id,
                'candidate_id' => $validated2['candidate_id']
            ]);
        }

        return redirect()->route('races.index')->with('success', 'Race created successfully');
    }

    public function edit(Race $race)
    {
        $candidates = Candidate::all();
        $states = State::all();
        // Build an array of ['candidate_id'=>…, 'party'=>…] for the form
        $rows = $race->raceCandidates()->with('candidate')->get()
            ->map(fn($pc) => [
                'candidate_id' => $pc->candidate_id,
                'party'        => $pc->candidate->party,
            ])
            ->toArray();

        $race->candidates = $rows;

        $approvalCandidate = $race->raceCandidates()->first();

        return view('admin.races.edit', compact('race', 'candidates', 'approvalCandidate', 'states'));
    }

    public function update(Request $request, Race $race)
    {
        if ($request->input('state_id') === 'general') {
            $request->merge(['state_id' => null]);
        }

         $request->merge([
            'is_featured' => $request->has('is_featured') ? 1 : 0,
        ]);

        $validated = $request->validate([
            'race'      => 'required|in:election,approval',
            'race_type'      => 'nullable|in:president,senate,house,governor,other',
            'state_id'      => 'nullable|exists:states,id',
            'district' => 'nullable|integer',
            'is_featured'      => 'nullable|integer|between:0,1',
            'election_round' => 'nullable|in:primary,general',
        ]);
        $race->update($validated);

        // Re-validate the candidate inputs
        $request->validate([
            'candidates.*.candidate_id' => 'nullable|exists:candidates,id',
            'candidate_id'              => 'nullable|exists:candidates,id',
        ]);

        if ($request->race === 'election') {
            // incoming IDs
            $incoming = collect($request->input('candidates', []))
                ->pluck('candidate_id')
                ->filter()
                ->unique()
                ->toArray();

            // existing IDs
            $existing = $race->raceCandidates()->pluck('candidate_id')->toArray();

            // which were removed?
            $removed = array_diff($existing, $incoming);

            if ($removed) {
                // delete pivot rows
                RaceCandidate::where('race_id', $race->id)
                    ->whereIn('candidate_id', $removed)
                    ->delete();

                // delete any results for those candidate_ids
                PollResult::whereIn('candidate_id', $removed)->delete();
            }

            // re-add the rest
            foreach ($incoming as $cid) {
                RaceCandidate::firstOrCreate([
                    'race_id'      => $race->id,
                    'candidate_id' => $cid,
                ]);
            }
        }
        // approval stays untouched
        if ($request->race === 'approval' && $request->filled('candidate_id')) {
            RaceCandidate::updateOrCreate(
                ['race_id' => $race->id],
                ['candidate_id' => $request->input('candidate_id')]
            );
        }

        return redirect()->route('races.index')
            ->with('success', 'Race updated successfully');
    }

    public function destroy(Race $race)
    {
        $race->delete();
        return redirect()->route('races.index')->with('success', 'Race deleted successfully');
    }
}
