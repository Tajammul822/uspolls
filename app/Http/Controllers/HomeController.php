<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Poll;
use App\Models\Race;
use App\Models\RaceApproval;
use App\Models\State;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        // 1) Dropdown data
        $states = State::orderBy('name')->get(['id', 'name']);
        $races  = Race::where('race', 'election')->orderBy('race')->get(['id', 'race']);

        $rac  = Race::where('race', 'approval')->orderBy('race')->get(['id', 'race']);

        // 2) Pick the single “approval” race (or first if you had multiples)
        $race = $rac->first();
        if (! $race) {
            $rawDates = $labels = $approvalData = $disapprovalData = [];
            $approvalStats = null;
        } else {
            $approvals = $race->raceApproval()->orderBy('race_date')->get();

            // Full ISO dates (for filtering)
            $rawDates = $approvals
                ->pluck('race_date')
                ->map(fn($d) => $d->toDateString())  // "2025-06-18"
                ->toArray();

            // Display labels (e.g. "Jun 18")
            $labels = array_map(
                fn($d) => Carbon::parse($d)->format('M d'),
                $rawDates
            );

            $approvalData    = $approvals->pluck('approve_rating')->toArray();
            $disapprovalData = $approvals->pluck('disapprove_rating')->toArray();

            $latest = $approvals->last();


            $approvalStats = $latest ? [
                'name'        => $latest->name,
                'date'        => $latest->race_date->format('M d, Y'),
                'sample_size' => $latest->sample_size,
                'approve'     => round($latest->approve_rating, 1),
                'disapprove'  => round($latest->disapprove_rating, 1),
                'net'         => round($latest->approve_rating - $latest->disapprove_rating, 1),
            ] : null;
        }


        $latestApprovals = $this->getLatestApprovals();

        return view('frontend.home', compact(
            'states',
            'races',
            'rawDates',
            'labels',
            'approvalData',
            'disapprovalData',
            'approvalStats',
            'latestApprovals'
        ));
    }


     protected function getLatestApprovals(): array
    {
        // 1) find the approval race
        $race = Race::where('race', 'approval')->first();
        if (! $race) {
            return [];
        }

        // 2) fetch and map
        return $race->raceApproval()
            ->orderBy('race_date', 'desc')
            ->get()
            ->map(fn($a) => [
                'pollster'    => $a->name,
                'rawDate'     => $a->race_date->toDateString(),        // "2025-06-18"
                'displayDate' => $a->race_date->format('M d, Y'),     // "Jun 18, 2025"
                'sampleSize'  => number_format($a->sample_size),      // "1,200"
                'approve'     => round($a->approve_rating, 1),        // 49.7
                'disapprove'  => round($a->disapprove_rating, 1),     // 47.5
                'net'         => round($a->approve_rating - $a->disapprove_rating, 1),
            ])
            ->toArray();
    }
    // Ajax: fetch polls for selected race
    public function pollsByRace($raceId)
    {
        $polls = Poll::where('race_id', $raceId)
            ->with(['candidate', 'pollster'])
            ->get();

        $data = $polls->map(fn($poll) => [
            'pollster' => $poll->pollster->name ?? 'N/A',
            'date'      => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
            'sample'    => $poll->sample_size,
            'results'   => $poll->candidate
                ->map(fn($c) => [
                    'name' => $c->name,
                    'pct'  => $c->pivot->result_percentage,
                ])
                ->sortByDesc('pct')
                ->values()
                ->toArray(),
        ]);

        return response()->json($data);
    }


    // AJAX: Fetch all polls for any race in this state
    public function pollsByState($stateId)
    {
        $polls = Poll::whereHas('race', fn($q) => $q->where('state_id', $stateId))
            ->with(['candidate', 'pollster'])
            ->get();

        $data = $polls->map(fn($poll) => [
            'pollster' => $poll->pollster->name ?? 'N/A',
            'date'      => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
            'sample'    => $poll->sample_size,
            // results: array of [name,pct], sorted descending
            'results'   => $poll->candidate
                ->map(fn($cand) => [
                    'name' => $cand->name,
                    'pct'  => $cand->pivot->result_percentage,
                ])
                ->sortByDesc('pct')->values()->toArray(),
        ]);

        return response()->json($data);
    }


    public function searchPolls(Request $request)
    {
        $q = $request->input('search');

        $pollIds = Candidate::where('name', 'like', "%{$q}%")
            ->join('poll_results', 'candidates.id', '=', 'poll_results.candidate_id')
            ->distinct()
            ->pluck('poll_results.poll_id');

        $results = Poll::whereIn('id', $pollIds)
            ->with('candidate')
            ->get()
            ->map(function ($poll) {
                $names = $poll->candidate->pluck('name')->toArray();
                sort($names);
                return [
                    'poll_id' => $poll->id,
                    'label'   => implode(' vs ', $names),
                    'key'     => implode('-', $names),
                ];
            })
            ->unique('key')
            ->values();

        return response()->json($results);
    }

    public function getResults($key)
    {
        $names = explode('-', $key);

        $polls = Poll::whereHas('candidate', function ($q) use ($names) {
            $q->whereIn('name', $names);
        }, '=', count($names))
            ->with(['candidate' => function ($q) use ($names) {
                $q->whereIn('name', $names);
            }, 'pollster'])
            ->get();

        $result = $polls->map(function ($poll) {
            return [
                'pollster_name' => $poll->pollster->name ?? 'N/A',
                'poll_date'     => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
                'sample_size'   => $poll->sample_size,
                'results'       => $poll->candidate->map(function ($cand) {
                    return [
                        'name' => $cand->name,
                        'pct'  => $cand->pivot->result_percentage,
                    ];
                })->values()
            ];
        });

        return response()->json([
            'candidate_names' => $names,
            'polls' => $result,
        ]);
    }
}
