<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Poll;
use App\Models\Pollster;
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
        $races = Race::where('race', 'election')->select('race_type')->distinct()->orderBy('race_type')->get();
        $pollesters  = Pollster::orderBy('name')->get(['id', 'name']);


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

        // 3) Featured polls table
        $featuredPolls = Poll::with(['pollster', 'results'])
            ->where('is_featured', 1)
            ->get()
            ->map(function ($poll) {
                $pct    = $poll->results->pluck('result_percentage')->sortDesc()->values();
                $top    = $pct->get(0, 0);
                $runner = $pct->get(1, 0);
                return [
                    'pollster'    => $poll->pollster->name,
                    'sample_size' => $poll->sample_size,
                    'net_margin'  => $top - $runner,
                ];
            });

        return view('frontend.home', compact(
            'states',
            'races',
            'pollesters',
            'rawDates',
            'labels',
            'approvalData',
            'disapprovalData',
            'approvalStats',
            'latestApprovals',
            'featuredPolls'
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
                'pollster'    => $a->pollster->name,
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
    public function pollsByRace($raceType)
    {


        $raceIds = Race::where('race_type', $raceType)
            ->pluck('id');

        // 2) Use whereIn instead of where:
        $polls = Poll::whereIn('race_id', $raceIds)
            ->with(['candidate', 'pollster'])
            ->get();

        // $data = $polls->map(fn($poll) => [
        //     'pollster' => $poll->pollster->name ?? 'N/A',
        //     'date'     => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
        //     'sample'   => $poll->sample_size,
        //     'results'  => $poll->candidate
        //         ->map(fn($c) => [
        //             'name' => $c->name,
        //             'pct'  => $c->pivot->result_percentage,
        //         ])
        //         ->sortByDesc('pct')
        //         ->values()
        //         ->toArray(),
        // ]);


        $data = $polls->map(fn($poll) => [
            'race_id'    => $poll->race_id,
            // e.g. a label like "Senate – Alabama Primary" or however you name it
            'race_label' => ($poll->race->state->name ?? '')
                . ' ' . $poll->race->election_round,

            'pollster'   => $poll->pollster->name ?? 'N/A',
            'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
            'sample'     => $poll->sample_size,
            'results'    => $poll->candidate
                ->map(fn($c) => [
                    'name' => $c->name,
                    'pct'  => (float) $c->pivot->result_percentage,
                    'party' => $c->party,
                ])
                ->sortByDesc('pct')
                ->values()
                ->toArray(),
        ]);

        return response()->json($data);



        ///OLD
        // $polls = Poll::where('race_id', $raceId) 
        //     ->with(['candidate', 'pollster'])
        //     ->get();

        // $data = $polls->map(fn($poll) => [
        //     'pollster' => $poll->pollster->name ?? 'N/A',
        //     'date'      => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
        //     'sample'    => $poll->sample_size,
        //     'results'   => $poll->candidate
        //         ->map(fn($c) => [
        //             'name' => $c->name,
        //             'pct'  => $c->pivot->result_percentage,
        //         ])
        //         ->sortByDesc('pct')
        //         ->values()
        //         ->toArray(),
        // ]);

        // return response()->json($data);
    }


    // AJAX: Fetch all polls for any race in this state
    // public function pollsByState($stateId)
    // {
    //     $polls = Poll::whereHas('race', fn($q) => $q->where('state_id', $stateId))
    //         ->with(['candidate', 'pollster'])
    //         ->get();

    //     $data = $polls->map(fn($poll) => [
    //         'pollster' => $poll->pollster->name ?? 'N/A',
    //         'date'      => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //         'sample'    => $poll->sample_size,
    //         // results: array of [name,pct], sorted descending
    //         'results'   => $poll->candidate
    //             ->map(fn($cand) => [
    //                 'name' => $cand->name,
    //                 'pct'  => $cand->pivot->result_percentage,
    //             ])
    //             ->sortByDesc('pct')->values()->toArray(),
    //     ]);

    //     return response()->json($data);
    // }


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
                sort($names, SORT_STRING);
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

    // public function getResults($key)
    // {
    //     $names = explode('-', $key);

    //     $polls = Poll::whereHas('candidate', function ($q) use ($names) {
    //         $q->whereIn('name', $names);
    //     }, '=', count($names))
    //         ->with(['candidate' => function ($q) use ($names) {
    //             $q->whereIn('name', $names);
    //         }, 'pollster'])
    //         ->get();

    //     $result = $polls->map(function ($poll) {
    //         return [
    //             'pollster_name' => $poll->pollster->name ?? 'N/A',
    //             'poll_date'     => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //             'sample_size'   => $poll->sample_size,
    //             'results'       => $poll->candidate->map(function ($cand) {
    //                 return [
    //                     'name' => $cand->name,
    //                     'pct'  => $cand->pivot->result_percentage,
    //                 ];
    //             })->values()
    //         ];
    //     });

    //     return response()->json([
    //         'candidate_names' => $names,
    //         'polls' => $result,
    //     ]);
    // }
    public function pollsByState($stateId)
    {
        $polls = Poll::whereHas('race', function ($q) use ($stateId) {
            $q->where('state_id', $stateId);
        })
            ->with(['candidate', 'pollster', 'race.state'])
            ->get();

        $data = $polls->map(fn($poll) => [
            'race_id'    => $poll->race_id,
            'race_label' => trim(
                ($poll->race->state->name ?? '')
                    . ' '
                    . $poll->race->election_round,
            ),
            'pollster'   => $poll->pollster->name ?? 'N/A',
            'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
            'sample'     => $poll->sample_size,
            'results'    => $poll->candidate
                ->map(fn($c) => [
                    'name' => $c->name,
                    'pct'  => (float) $c->pivot->result_percentage,
                    'party' => $c->party,
                ])
                ->sortByDesc('pct')
                ->values()
                ->toArray(),
        ]);

        return response()->json($data);
    }

    public function pollsByCandidate($candidateKey)
    {
        // $candidateKey is something like "Alice-Bob-Charlie"
        $names = explode('-', $candidateKey);
        // Trim whitespace if any
        $names = array_map('trim', $names);
        if (empty($names)) {
            return response()->json(['polls' => []]);
        }

        // Fetch candidate IDs for these names
        $candidateIds = Candidate::whereIn('name', $names)->pluck('id')->toArray();
        if (count($candidateIds) !== count($names)) {
            // Some name not found; return empty or handle as needed
            return response()->json(['polls' => []]);
        }

        // Query polls that include *all* these candidates
        $pollsQuery = Poll::query();
        foreach ($candidateIds as $cid) {
            $pollsQuery->whereHas('candidate', function ($q) use ($cid) {
                $q->where('candidates.id', $cid);
            });
        }
        $polls = $pollsQuery
            ->with(['candidate', 'pollster', 'race.state'])
            ->get();

        $data = $polls->map(fn($poll) => [
            'race_id'    => $poll->race_id,
            'race_label' => trim(
                ($poll->race->state->name ?? '')
                    . ' '
                    . $poll->race->election_round
                    . ' '
                    . $poll->race->race_type
            ),
            'pollster'   => $poll->pollster->name ?? 'N/A',
            'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
            'sample'     => $poll->sample_size,
            'results'    => $poll->candidate
                ->map(fn($c) => [
                    'name' => $c->name,
                    'pct'  => (float) $c->pivot->result_percentage,
                    'party' => $c->party,
                ])
                ->sortByDesc('pct')
                ->values()
                ->toArray(),
        ]);

        return response()->json(['polls' => $data]);
    }

    public function filterPolls(Request $request)
    {
        $params = $request->validate([
            'pollType'    => 'required|string',
            'state_id'    => 'nullable|integer',
            'pollster_id' => 'nullable|integer',
            'timeframe'   => 'required|integer', // in days
        ]);

        $params['pollType'] = strtolower($params['pollType']);

        // Get race ID
        $raceId = Race::where('race_type', $params['pollType'])
            ->when($params['state_id'], fn($q) => $q->where('state_id', $params['state_id']))
            ->value('id');

        if (!$raceId) {
            return response()->json([]);
        }

        // Fetch polls with candidates and pollster
        $polls = Poll::with(['pollster', 'candidate'])
            ->where('race_id', $raceId)
            ->when($params['pollster_id'], fn($q) => $q->where('pollster_id', $params['pollster_id']))
            ->when($params['timeframe'], fn($q) => $q->where('poll_date', '>=', now()->subDays($params['timeframe'])))
            ->orderBy('poll_date', 'desc')
            ->get();

        // Transform response
        $result = $polls->map(function ($poll) {
            $results = $poll->candidate->map(function ($cand) {
                return [
                    'name' => $cand->name,
                    'pct'  => $cand->pivot->result_percentage,
                ];
            })->sortByDesc('pct')->values();

            $top = $results[0]['pct'] ?? 0;
            $second = $results[1]['pct'] ?? 0;
            $net = round($top - $second, 1);

            return [
                'pollster' => $poll->pollster->name ?? 'N/A',
                'date'     => $poll->poll_date,
                'sample'   => $poll->sample_size,
                'net'      => $net,
            ];
        });

        return response()->json($result);
    }
}
