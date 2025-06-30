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
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{

    public function index(Request $request)
    {

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


        $featuredRaces = Race::where('is_featured', 1)->with('state', 'candidates')->get();


        if ($request->ajax()) {
            return view('frontend.approval-cards', compact('latestApprovals'))->render();
        }

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
            'featuredRaces'
        ));
    }


    protected function getLatestApprovals(): LengthAwarePaginator
    {
        $perPage = 8;

        // Paginate approval races
        $races = Race::where('race', 'approval')
            ->with(['candidates:id,name'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Transform items before returning
        $races->getCollection()->transform(function ($race) {
            return [
                'race_id'    => $race->id,
                'candidates' => $race->candidates->pluck('name')->toArray(),
            ];
        });

        return $races;
    }




    public function approvalDetails($race_id)
    {
        // Load the full Race data including related RaceApprovals
        $race = Race::findOrFail($race_id);

        // Get all approval records related to this race
        $records = $race->raceApproval()
            ->with('pollster') // Ensure pollster relationship is eager loaded
            ->orderByDesc('race_date')
            ->get()
            ->map(function ($a) {
                return [
                    'pollster'    => $a->pollster->name ?? 'N/A',
                    'rawDate'     => $a->race_date->toDateString(),
                    'displayDate' => $a->race_date->format('M d, Y'),
                    'sampleSize'  => number_format($a->sample_size),
                    'approve'     => round($a->approve_rating, 1),
                    'disapprove'  => round($a->disapprove_rating, 1),
                    'net'         => round($a->approve_rating - $a->disapprove_rating, 1),
                ];
            })
            ->values();
        return view('frontend.approvaldetails', compact('race', 'records'));
    }



    // public function apiIndex(Request $request)
    // {
    //     $q = Race::query();

    //     if ($rt = $request->query('race_type')) {
    //         $q->where('race_type', $rt);
    //     }
    //     if ($sid = $request->query('state_id')) {
    //         $q->where('state_id', $sid);
    //     }

    //     $races = $q->with('state')  // eager-load your state relationship
    //         ->get(['id', 'race_type', 'election_round', 'state_id', 'status'])
    //         ->map(function ($r) {
    //             return [
    //                 'id'             => $r->id,
    //                 'race_type'      => ucfirst($r->race_type),
    //                 'election_round' => $r->election_round,
    //                 'state_name'     => $r->state->name,
    //                 'status'         => $r->status,
    //             ];
    //         });

    //     return response()->json($races);
    // }


    public function apiIndex(Request $request)
    {
        $rt  = $request->query('race_type');
        $sid = $request->query('state_id', null);

        $q = Race::query();

        // Special case: All States (i.e. global elections)
        if ($sid === 'temp') {
            $q->where('race', 'election')->whereNull('state_id');
        } else {
            // Apply race_type filter if provided
            if (!empty($rt)) {
                $q->where('race_type', $rt);
            }

            // Apply state_id filter only if a real state ID is selected
            if (!empty($sid)) {
                $q->where('state_id', $sid);
            }
        }

        $races = $q->with('state')
            ->get(['id', 'race_type', 'election_round', 'state_id', 'status'])
            ->map(function ($r) {
                return [
                    'id'             => $r->id,
                    'race_type'      => ucfirst($r->race_type),
                    'election_round' => $r->election_round,
                    'state_name'     => $r->state_id
                        ? $r->state->name
                        : 'All States',
                    'status'         => $r->status,
                ];
            });

        return response()->json($races);
    }


    public function show(Request $request)
    {
        $raceId = $request->race_id;

        // Load race with its candidates
        $race = Race::with('candidates')->findOrFail($raceId);
        $isPrimary = ($race->election_round === 'primary');

        // Generate a single color per candidate (only for primary)
        $candidateColors = [];
        if ($isPrimary) {
            foreach ($race->candidates as $c) {
                $candidateColors[$c->id] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            }
        }

        // Predefined party colors for general election
        $partyColors = [
            'Democratic Party'  => 'blue',
            'Republican Party'  => 'red',
            'Libertarian Party' => '#F39835',
            'Green Party'       => 'green',
        ];

        // Fetch related polls
        $polls = Poll::where('race_id', $raceId)
            ->with(['candidate', 'pollster', 'race.state'])
            ->get();

        // Build data for view
        $data = $polls->map(function ($poll) use ($isPrimary, $candidateColors, $partyColors) {
            // Build results with consistent colors
            $results = $poll->candidate->map(function ($c) use ($isPrimary, $candidateColors, $partyColors) {
                $color = $isPrimary
                    ? ($candidateColors[$c->id] ?? 'gray')
                    : ($partyColors[$c->party] ?? 'gray');

                return [
                    'id'    => $c->id,
                    'name'  => $c->name,
                    'pct'   => (float) $c->pivot->result_percentage,
                    'color' => $color,
                ];
            })->toArray();

            // Sort for net margin
            $sorted = collect($results)->sortByDesc('pct')->values();
            $net = round(($sorted->get(0)['pct'] ?? 0) - ($sorted->get(1)['pct'] ?? 0), 1);
            $netColor = $sorted->get(0)['color'] ?? 'gray';

            return [
                'race_id'   => $poll->race_id,
                'race_type' => $poll->race->race_type,
                'race_label' => trim(($poll->race->state->name ?? '') . ' ' . $poll->race->election_round),
                'pollster'  => $poll->pollster->name ?? 'N/A',
                'date'      => Carbon::parse($poll->poll_date)->format('Y-m-d'),
                'sample'    => $poll->sample_size,
                'results'   => $results,
                'net'       => $net,
                'net_color' => $netColor,
            ];
        });

        $featuredRaces = Race::where('is_featured', 1)
            ->with('state', 'candidates')
            ->get();

        // Select view based on election round
        $view = $isPrimary ? 'frontend.primarydetails' : 'frontend.details';

        return view($view, compact('data', 'featuredRaces'));
    }


    // public function show(Request $request)
    // {
    //     $raceIds = $request->race_id;
    //     $polls = Poll::where('race_id', $raceIds)
    //         ->with(['candidate', 'pollster'])
    //         ->get();


    //     $primaryIds = Race::where('election_round', 'primary')->where('id', $raceIds)->pluck('id');
    //     if ($raceIds == $primaryIds){

    //     }
    //     $partyColors = [
    //         'Democratic Party'  => 'blue',
    //         'Republican Party'  => 'red',
    //         'Libertarian Party' => '#F39835',
    //         'Green Party'       => 'green',
    //     ];

    //     $data = $polls->map(function ($poll) use ($partyColors) {
    //         // 1) build unsorted results array
    //         $unsorted = $poll->candidate->map(function ($c) use ($partyColors) {
    //             return [
    //                 'name'  => $c->name,
    //                 'pct'   => (float)$c->pivot->result_percentage,
    //                 'party' => $c->party,
    //                 'color' => $partyColors[$c->party] ?? 'gray',
    //             ];
    //         })->toArray();

    //         // 2) separately sort a copy for net margin
    //         $sorted = collect($unsorted)
    //             ->sortByDesc('pct')
    //             ->values();

    //         $top      = $sorted->get(0)['pct'] ?? 0;
    //         $runnerUp = $sorted->get(1)['pct'] ?? 0;
    //         $net      = round($top - $runnerUp, 1);
    //         // net color = color of the top candidate
    //         $netColor = $sorted->get(0)['color'] ?? 'gray';

    //         return [
    //             'race_id'    => $poll->race_id,
    //             'race_type'    => $poll->race->race_type,
    //             'race_label' => ($poll->race->state->name ?? '') . ' ' . $poll->race->election_round,
    //             'pollster'   => $poll->pollster->name ?? 'N/A',
    //             'date'       => Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //             'sample'     => $poll->sample_size,
    //             // **unsorted** so each name lines up with its own pct
    //             'results'    => $unsorted,
    //             'net'        => $net,
    //             'net_color'  => $netColor,
    //         ];
    //     });


    //     $featuredRaces = Race::where('is_featured', 1)->with('state', 'candidates')->get();

    //     return view('frontend.details', compact('data', 'featuredRaces'));
    // }


    public function filterOptions(Request $request)
    {
        $request->validate(['pollType' => 'required|string']);
        $type = strtolower($request->input('pollType'));


        // Race IDs for this type
        $raceIds = Race::where('race_type', $type)->pluck('id');

        $stateIds = Race::where('race_type', $type)
            ->pluck('state_id')
            ->unique();

        $states   = State::whereIn('id', $stateIds)
            ->orderBy('name')
            ->get(['id', 'name']);


        // Pollester IDs from those races’ polls
        $pollesterIds = Poll::whereIn('race_id', $raceIds)
            ->pluck('pollster_id')
            ->unique()
            ->filter();
        $pollesters = Pollster::whereIn('id', $pollesterIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'states'    => $states,
            'pollesters' => $pollesters,
        ]);
    }

    // public function filterPolls(Request $request)
    // {
    //     $params = $request->validate([
    //         'pollType'    => 'required|string',
    //         'state_id'    => 'nullable|integer',
    //         'pollster_id' => 'nullable|integer',
    //         'timeframe'   => 'required|integer',
    //     ]);

    //     dd($params);
    //     $pollType = strtolower($params['pollType']);
    //     //     $raceId = Race::where('race_type', $pollType)
    //     //         ->when($params['state_id'], fn($q) => $q->where('state_id', $params['state_id']))
    //     //         ->value('id');

    //     //     if (! $raceId) {
    //     //         return response()->json([]);
    //     //     }

    //     //     $polls = Poll::with(['pollster', 'candidate', 'race'])
    //     //         ->where('race_id', $raceId)
    //     //         ->when($params['pollster_id'], fn($q) => $q->where('pollster_id', $params['pollster_id']))
    //     //         ->when($params['timeframe'], fn($q) => $q->where('poll_date', '>=', now()->subDays($params['timeframe'])))
    //     //         ->orderBy('poll_date', 'desc')
    //     //         ->get();

    //     //     $result = $polls->map(fn($poll) => [
    //     //         'race'           => ucfirst($poll->race->race_type),
    //     //         'pollster'       => $poll->pollster->name ?? 'N/A',
    //     //         'date'           => $poll->poll_date->format('Y-m-d'),
    //     //         'dateFormatted'  => $poll->poll_date->format('D, j M'),
    //     //         'result'         => tap(
    //     //             $poll->candidate
    //     //                 ->sortByDesc(fn($c) => $c->pivot->result_percentage)
    //     //                 ->take(2)
    //     //                 ->map(fn($c) => "{$c->name} {$c->pivot->result_percentage}%")
    //     //                 ->implode(' – '),
    //     //             fn(&$s) => $s = $s ?: 'N/A'
    //     //         ),
    //     //         'spread'         => round(
    //     //             ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->first() ?? 0)
    //     //                 -
    //     //                 ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->skip(1)->first() ?? 0),
    //     //             1
    //     //         ),
    //     //         'race_id'        => $poll->race_id,
    //     //     ]);

    //     //     return response()->json($result);
    //     $raceIds = Race::where('race_type', $pollType)
    //         ->when(!is_null($params['state_id']), function ($q) use ($params) {
    //             $q->where('state_id', $params['state_id']);
    //         })
    //         ->pluck('id');

    //     $polls = Poll::with(['candidate' => fn($q) => $q->withPivot('result_percentage')])
    //         ->whereIn('race_id', $raceIds)
    //         ->when($params['pollster_id'], fn($q) => $q->where('pollster_id', $params['pollster_id']))
    //         ->when($params['timeframe'], fn($q) => $q->where('poll_date', '>=', now()->subDays($params['timeframe'])))
    //         ->orderBy('poll_date', 'desc')
    //         // ->latest('poll_date')
    //         ->get();

    //     $result = $polls->map(fn($poll) => [
    //         'race'           => ucfirst($poll->race->race_type),
    //         'pollster'       => $poll->pollster->name ?? 'N/A',
    //         'date'           => $poll->poll_date->format('Y-m-d'),
    //         'dateFormatted'  => $poll->poll_date->format('D, j M'),
    //         'result'         => tap(
    //             $poll->candidate
    //                 ->sortByDesc(fn($c) => $c->pivot->result_percentage)
    //                 ->take(2)
    //                 ->map(fn($c) => "{$c->name} {$c->pivot->result_percentage}%")
    //                 ->implode(' – '),
    //             fn(&$s) => $s = $s ?: 'N/A'
    //         ),
    //         'spread'         => round(
    //             ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->first() ?? 0)
    //                 -
    //                 ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->skip(1)->first() ?? 0),
    //             1
    //         ),
    //         'race_id'        => $poll->race_id,
    //     ]);

    //     return response()->json($result);
    // }

    public function filterPolls(Request $request)
    {
        $params = $request->validate([
            'pollType'    => 'required|string',
            'state_id'    => 'nullable|integer',
            'pollster_id' => 'nullable|integer',
            'timeframe'   => 'required|integer',
        ]);

        $pollType = strtolower($params['pollType']);

        // 1) figure out which race IDs to include (single state or ALL for that type)
        $raceIds = Race::where('race_type', $pollType)
            ->when($params['state_id'], fn($q) => $q->where('state_id', $params['state_id']))
            ->pluck('id');

        // 2) pull polls with all your filters
        $polls = Poll::with([
            'candidate' => fn($q) => $q->withPivot('result_percentage'),
            'race',
            'pollster',
        ])
            ->whereIn('race_id', $raceIds)
            ->when(
                $params['pollster_id'],
                fn($q) =>
                $q->where('pollster_id', $params['pollster_id'])
            )
            ->when(
                $params['timeframe'],
                fn($q) =>
                $q->where('poll_date', '>=', now()->subDays($params['timeframe']))
            )
            ->orderBy('poll_date', 'desc')
            ->get();

        // 3) map into your exact JSON shape
        $result = $polls->map(fn($poll) => [
            'race'          => ucfirst($poll->race->race_type),
            'pollster'      => $poll->pollster->name ?? 'N/A',
            'date'          => $poll->poll_date->format('Y-m-d'),
            'dateFormatted' => $poll->poll_date->format('D, j M'),
            'result'        => tap(
                $poll->candidate
                    ->sortByDesc(fn($c) => $c->pivot->result_percentage)
                    ->take(2)
                    ->map(fn($c) => "{$c->name} {$c->pivot->result_percentage}%")
                    ->implode(' – '),
                fn(&$s) => $s = $s ?: 'N/A'
            ),
            'spread'        => round(
                ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->first() ?? 0)
                    -
                    ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->skip(1)->first() ?? 0),
                1
            ),
            'race_id'       => $poll->race_id,
        ]);
        return response()->json($result);
    }

    public function getPollstersByState(Request $request)
    {
        $type     = strtolower($request->input('pollType'));
        $stateId  = $request->input('state_id');

        // Get all races for this type and state
        $raceIds = Race::where('race_type', $type)
            ->when($stateId, fn($q) => $q->where('state_id', $stateId))
            ->pluck('id');

        // Get all pollster IDs from polls of those races
        $pollsterIds = Poll::whereIn('race_id', $raceIds)
            ->pluck('pollster_id')
            ->unique();

        $pollsters = Pollster::whereIn('id', $pollsterIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'pollesters' => $pollsters
        ]);
    }

    // public function filterPolls(Request $request)
    // {
    //     $params = $request->validate([
    //         'pollType'    => 'required|string',
    //         'state_id'    => 'nullable|integer',
    //         'pollster_id' => 'nullable|integer',
    //         'timeframe'   => 'required|integer',
    //     ]);

    //     $pollType = strtolower($params['pollType']);

    //     $raceId = Race::where('race_type', $pollType)
    //         ->when($params['state_id'], fn($q) => $q->where('state_id', $params['state_id']))
    //         ->value('id');

    //     if (! $raceId) {
    //         return response()->json([]);
    //     }

    //     $polls = Poll::with(['pollster', 'candidate'])
    //         ->where('race_id', $raceId)
    //         ->when($params['pollster_id'], fn($q) => $q->where('pollster_id', $params['pollster_id']))
    //         ->when($params['timeframe'], fn($q) => $q->where('poll_date', '>=', now()->subDays($params['timeframe'])))
    //         ->orderBy('poll_date', 'desc')
    //         ->get();

    //     $result = $polls->map(fn($poll) => [
    //         'pollster' => $poll->pollster->name ?? 'N/A',
    //         'date'     => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //         'sample'   => $poll->sample_size,
    //         'net'      => round(
    //             (
    //                 ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->first() ?? 0)
    //                 -
    //                 ($poll->candidate->pluck('pivot.result_percentage')->sortDesc()->skip(1)->first() ?? 0)
    //             ),
    //             1
    //         ),
    //     ]);

    //     return response()->json($result);
    // }


    // protected function getLatestApprovals(): array
    // {
    //     // 1) Get all approval races
    //     $races = Race::where('race', 'approval')->getAll();

    //     if ($races->isEmpty()) {
    //         return [];
    //     }
    //     return $races;


    //         // 2) Fetch all related race approvals, flatten them, sort by race_date, and map
    //         return $races->flatMap(function ($race) {
    //             return $race->raceApproval()->get();
    //         })
    //             ->sortByDesc('race_date')
    //             ->map(function ($a) {
    //                 return [
    //                     'pollster'    => $a->pollster->name,
    //                     'rawDate'     => $a->race_date->toDateString(),
    //                     'displayDate' => $a->race_date->format('M d, Y'),
    //                     'sampleSize'  => number_format($a->sample_size),
    //                     'approve'     => round($a->approve_rating, 1),
    //                     'disapprove'  => round($a->disapprove_rating, 1),
    //                     'net'         => round($a->approve_rating - $a->disapprove_rating, 1),
    //                 ];
    //             })
    //             ->values()
    //             ->toArray();
    // }


    // protected function getLatestApprovals(): array
    // {
    //     // 1) Grab only “approval” races, eager-load their candidates
    //     $races = Race::where('race', 'approval')
    //         ->with(['candidates:id,name'])  // assumes a Race→candidates() relation
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     // 2) Map to simple arrays
    //     return $races->map(fn($race) => [
    //         'race_id'    => $race->id,
    //         'candidates' => $race->candidates->pluck('name')->toArray(),
    //     ])->toArray();
    // }


    // public function pollsByRace($raceType)
    // {
    //     // 

    //     // // 2) Use whereIn instead of where:
    //     // $polls = Poll::whereIn('race_id', $raceIds)
    //     //     ->with(['candidate', 'pollster'])
    //     //     ->get();

    //     // $data = $polls->map(fn($poll) => [
    //     //     'race_id'    => $poll->race_id,
    //     //     'race_label' => ($poll->race->state->name ?? '') . ' ' . $poll->race->election_round,
    //     //     'pollster'   => $poll->pollster->name ?? 'N/A',
    //     //     'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //     //     'sample'     => $poll->sample_size,
    //     //     'results'    => $poll->candidate
    //     //         ->map(fn($c) => [
    //     //             'name' => $c->name,
    //     //             'pct'  => (float) $c->pivot->result_percentage,
    //     //             'party' => $c->party,
    //     //         ])
    //     //         ->sortByDesc('pct')
    //     //         ->values()
    //     //         ->toArray(),
    //     // ]);

    //     // return response()->json($data);

    //     ///OLD
    //     // $polls = Poll::where('race_id', $raceId) 
    //     //     ->with(['candidate', 'pollster'])
    //     //     ->get();

    //     // $data = $polls->map(fn($poll) => [
    //     //     'pollster' => $poll->pollster->name ?? 'N/A',
    //     //     'date'      => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //     //     'sample'    => $poll->sample_size,
    //     //     'results'   => $poll->candidate
    //     //         ->map(fn($c) => [
    //     //             'name' => $c->name,
    //     //             'pct'  => $c->pivot->result_percentage,
    //     //         ])
    //     //         ->sortByDesc('pct')
    //     //         ->values()
    //     //         ->toArray(),
    //     // ]);

    //     // return response()->json($data);
    // }

    // public function pollsByState($stateId)
    // {
    //     $polls = Poll::whereHas('race', function ($q) use ($stateId) {
    //         $q->where('state_id', $stateId);
    //     })
    //         ->with(['candidate', 'pollster', 'race.state'])
    //         ->get();

    //     $data = $polls->map(fn($poll) => [
    //         'race_id'    => $poll->race_id,
    //         'race_label' => trim(
    //             ($poll->race->state->name ?? '')
    //                 . ' '
    //                 . $poll->race->election_round,
    //         ),
    //         'pollster'   => $poll->pollster->name ?? 'N/A',
    //         'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //         'sample'     => $poll->sample_size,
    //         'results'    => $poll->candidate
    //             ->map(fn($c) => [
    //                 'name' => $c->name,
    //                 'pct'  => (float) $c->pivot->result_percentage,
    //                 'party' => $c->party,
    //             ])
    //             ->sortByDesc('pct')
    //             ->values()
    //             ->toArray(),
    //     ]);

    //     return response()->json($data);
    // }



    // Ajax: fetch polls for selected race
    // public function pollsByRace($raceType)
    // {


    //     $raceIds = Race::where('race_type', $raceType)
    //         ->pluck('id');

    //     // 2) Use whereIn instead of where:
    //     $polls = Poll::whereIn('race_id', $raceIds)
    //         ->with(['candidate', 'pollster'])
    //         ->get();

    //     // $data = $polls->map(fn($poll) => [
    //     //     'pollster' => $poll->pollster->name ?? 'N/A',
    //     //     'date'     => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //     //     'sample'   => $poll->sample_size,
    //     //     'results'  => $poll->candidate
    //     //         ->map(fn($c) => [
    //     //             'name' => $c->name,
    //     //             'pct'  => $c->pivot->result_percentage,
    //     //         ])
    //     //         ->sortByDesc('pct')
    //     //         ->values()
    //     //         ->toArray(),
    //     // ]);


    //     $data = $polls->map(fn($poll) => [
    //         'race_id'    => $poll->race_id,
    //         // e.g. a label like "Senate – Alabama Primary" or however you name it
    //         'race_label' => ($poll->race->state->name ?? '')
    //             . ' ' . $poll->race->election_round,

    //         'pollster'   => $poll->pollster->name ?? 'N/A',
    //         'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //         'sample'     => $poll->sample_size,
    //         'results'    => $poll->candidate
    //             ->map(fn($c) => [
    //                 'name' => $c->name,
    //                 'pct'  => (float) $c->pivot->result_percentage,
    //                 'party' => $c->party,
    //             ])
    //             ->sortByDesc('pct')
    //             ->values()
    //             ->toArray(),
    //     ]);

    //     return response()->json($data);



    //     ///OLD
    //     // $polls = Poll::where('race_id', $raceId) 
    //     //     ->with(['candidate', 'pollster'])
    //     //     ->get();

    //     // $data = $polls->map(fn($poll) => [
    //     //     'pollster' => $poll->pollster->name ?? 'N/A',
    //     //     'date'      => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //     //     'sample'    => $poll->sample_size,
    //     //     'results'   => $poll->candidate
    //     //         ->map(fn($c) => [
    //     //             'name' => $c->name,
    //     //             'pct'  => $c->pivot->result_percentage,
    //     //         ])
    //     //         ->sortByDesc('pct')
    //     //         ->values()
    //     //         ->toArray(),
    //     // ]);

    //     // return response()->json($data);
    // }

    //   public function pollsByState($stateId)
    // {
    //     $polls = Poll::whereHas('race', function ($q) use ($stateId) {
    //         $q->where('state_id', $stateId);
    //     })
    //         ->with(['candidate', 'pollster', 'race.state'])
    //         ->get();

    //     $data = $polls->map(fn($poll) => [
    //         'race_id'    => $poll->race_id,
    //         'race_label' => trim(
    //             ($poll->race->state->name ?? '')
    //                 . ' '
    //                 . $poll->race->election_round,
    //         ),
    //         'pollster'   => $poll->pollster->name ?? 'N/A',
    //         'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //         'sample'     => $poll->sample_size,
    //         'results'    => $poll->candidate
    //             ->map(fn($c) => [
    //                 'name' => $c->name,
    //                 'pct'  => (float) $c->pivot->result_percentage,
    //                 'party' => $c->party,
    //             ])
    //             ->sortByDesc('pct')
    //             ->values()
    //             ->toArray(),
    //     ]);

    //     return response()->json($data);
    // }


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


    // public function searchPolls(Request $request)
    // {
    //     $q = $request->input('search');
    //     $pollIds = Candidate::where('name', 'like', "%{$q}%")
    //         ->join('poll_results', 'candidates.id', '=', 'poll_results.candidate_id')
    //         ->distinct()
    //         ->pluck('poll_results.poll_id');

    //     $results = Poll::whereIn('id', $pollIds)
    //         ->with('candidate')
    //         ->get()
    //         ->map(function ($poll) {
    //             $names = $poll->candidate->pluck('name')->toArray();
    //             sort($names, SORT_STRING);
    //             return [
    //                 'poll_id' => $poll->id,
    //                 'label'   => implode(' vs ', $names),
    //                 'key'     => implode('-', $names),
    //             ];
    //         })
    //         ->unique('key')
    //         ->values();

    //     return response()->json($results);
    // }

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


    // public function pollsByCandidate($candidateKey)
    // {
    //     // $candidateKey is something like "Alice-Bob-Charlie"
    //     $names = explode('-', $candidateKey);
    //     // Trim whitespace if any
    //     $names = array_map('trim', $names);
    //     if (empty($names)) {
    //         return response()->json(['polls' => []]);
    //     }

    //     // Fetch candidate IDs for these names
    //     $candidateIds = Candidate::whereIn('name', $names)->pluck('id')->toArray();
    //     if (count($candidateIds) !== count($names)) {
    //         // Some name not found; return empty or handle as needed
    //         return response()->json(['polls' => []]);
    //     }

    //     // Query polls that include *all* these candidates
    //     $pollsQuery = Poll::query();
    //     foreach ($candidateIds as $cid) {
    //         $pollsQuery->whereHas('candidate', function ($q) use ($cid) {
    //             $q->where('candidates.id', $cid);
    //         });
    //     }
    //     $polls = $pollsQuery
    //         ->with(['candidate', 'pollster', 'race.state'])
    //         ->get();

    //     $data = $polls->map(fn($poll) => [
    //         'race_id'    => $poll->race_id,
    //         'race_label' => trim(
    //             ($poll->race->state->name ?? '')
    //                 . ' '
    //                 . $poll->race->election_round
    //                 . ' '
    //                 . $poll->race->race_type
    //         ),
    //         'pollster'   => $poll->pollster->name ?? 'N/A',
    //         'date'       => \Carbon\Carbon::parse($poll->poll_date)->format('Y-m-d'),
    //         'sample'     => $poll->sample_size,
    //         'results'    => $poll->candidate
    //             ->map(fn($c) => [
    //                 'name' => $c->name,
    //                 'pct'  => (float) $c->pivot->result_percentage,
    //                 'party' => $c->party,
    //             ])
    //             ->sortByDesc('pct')
    //             ->values()
    //             ->toArray(),
    //     ]);

    //     return response()->json(['polls' => $data]);
    // // }
}
