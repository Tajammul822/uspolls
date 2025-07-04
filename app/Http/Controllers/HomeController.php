<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Poll;
use App\Models\Pollster;
use App\Models\Race;
use App\Models\RaceApproval;
use Exception;
use App\Models\State;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;


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
            ->with(['candidates:id,name,image'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Transform items before returning
        $races->getCollection()->transform(function ($race) {
            return [
                'race_id'    => $race->id,
                'candidates' => $race->candidates->pluck('name', 'image')->toArray(),
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


    public function apiIndex(Request $request)
    {
        // Pull your filters
        $rt  = $request->query('race_type');
        $sid = $request->query('state_id');

        // Build the base query: all election races, plus state name, poll & candidate info
        $rows = DB::table('races as r')
            ->select(
                'r.id',
                'r.race_type',
                'r.election_round',
                'r.state_id',
                'r.district',
                's.name as state_name',
                'p.id as poll_id',
                'p.pollster_id',
                'pr.result_percentage',
                'c.name as candidate_name',
                'c.party as candidate_party'
            )
            ->leftJoin('states as s', 'r.state_id', '=', 's.id')
            ->leftJoin('polls as p',  'r.id',       '=', 'p.race_id')
            ->leftJoin('poll_results as pr', 'p.id', '=', 'pr.poll_id')
            ->leftJoin('candidates as c',    'pr.candidate_id', '=', 'c.id')
            ->where('r.race', 'election');

        // Optional filters
        if (! empty($rt)) {
            $rows->where('r.race_type', $rt);
        }
        if ($sid === 'temp') {
            $rows->whereNull('r.state_id');
        } elseif (! empty($sid)) {
            $rows->where('r.state_id', $sid);
        }

        $rows = $rows->get();

        // Now group by race id
        $grouped = [];
        foreach ($rows as $row) {
            $rid = $row->id;
            if (! isset($grouped[$rid])) {
                $grouped[$rid] = [
                    'id'             => $rid,
                    'race_type'      => ucfirst($row->race_type),
                    'election_round' => $row->election_round,
                    'state_name'     => $row->state_name ?? 'All States',
                    'district'       => $row->district,
                    'candidates'     => [],   // will hold all candidates & percentages
                ];
            }
            // if there's a result for this row, add it
            if (! is_null($row->candidate_name)) {
                $grouped[$rid]['candidates'][] = [
                    'name'       => $row->candidate_name,
                    'percentage' => round($row->result_percentage, 1),
                    'party'      => $row->candidate_party,
                ];
            }
        }

        // For each race, take top two by percentage into your “leading” array
        $payload = array_map(function ($race) {
            $all = collect($race['candidates'])
                ->sortByDesc('percentage')
                ->take(2)
                ->values()
                ->all();

            return [
                'id'             => $race['id'],
                'race_type'      => $race['race_type'],
                'election_round' => $race['election_round'],
                'state_name'     => $race['state_name'],
                'district'       => $race['district'],
                'leading'        => $all,
            ];
        }, $grouped);

        return response()->json(array_values($payload));
    }

    public function show(Request $request)
    {
        $raceId = $request->race_id;

        // Load race and its candidates
        $race = Race::with('candidates')->findOrFail($raceId);
        $isPrimary = ($race->election_round === 'primary');

        // Prepare colors
        if ($isPrimary) {
            $palette = [
                '#f26419',
                '#054a91',
                '#8f2d56',
                '#707070',
                '#3D7317',
                '#9d53ff',
                '#329b29fb',
                '#633708',
                '#bf0603',
                '#3f7779',
                '#f73639',
                '#ef476f',
            ];
            $candidateIds = $race->candidates->pluck('id')->values();
            $candidateColors = $candidateIds->mapWithKeys(function ($id, $index) use ($palette) {
                $color = $palette[$index % count($palette)];
                return [$id => $color];
            })->all();
        } else {
            // Fixed party colors
            $candidateColors = [
                'Democratic Party'  => 'blue',
                'Republican Party'  => 'red',
                'Libertarian Party' => '#F39835',
                'Green Party'       => 'green',
            ];
        }

        // Fetch polls
        $polls = Poll::where('race_id', $raceId)
            ->with(['candidate', 'pollster', 'race.state'])
            ->get();

        // Build data array
        $data = $polls->map(function ($poll) use ($isPrimary, $candidateColors) {
            // Map each candidate in this poll
            $results = $poll->candidate->map(fn($c) => [
                'id'      => $c->id,
                'name'    => $c->name,
                'party'   => $c->party,
                'image'   => $c->image,
                'pct'     => (float) $c->pivot->result_percentage,
                // primary: lookup by ID; general: lookup by party name
                'color'   => $isPrimary
                    ? ($candidateColors[$c->id] ?? 'gray')
                    : ($candidateColors[$c->party] ?? 'gray'),
            ])->toArray();

            // Compute net margin & color
            $sorted   = collect($results)->sortByDesc('pct')->values();
            $net      = round(($sorted->get(0)['pct'] ?? 0) - ($sorted->get(1)['pct'] ?? 0), 1);
            $netColor = $sorted->get(0)['color'] ?? 'gray';

            return [
                'race_id'    => $poll->race_id,
                'race_type'  => $poll->race->race_type,
                'race_label' => trim(($poll->race->state->name ?? '') . ' ' . $poll->race->election_round),
                'pollster'   => $poll->pollster->name ?? 'N/A',
                'date'       => Carbon::parse($poll->poll_date)->format('Y-m-d'),
                'sample'     => $poll->sample_size,
                'results'    => $results,
                'net'        => $net,
                'net_color'  => $netColor,
            ];
        });

        $featuredRaces = Race::where('is_featured', 1)
            ->with('state', 'candidates')
            ->get();

        // Pick view based on round
        $view = $isPrimary
            ? 'frontend.primarydetails'
            : 'frontend.details';

        return view($view, compact('data', 'featuredRaces'));
    }


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

    //     $pollType = strtolower($params['pollType']);

    //     // 1) figure out which race IDs to include (single state or ALL for that type)
    //     $raceIds = Race::where('race_type', $pollType)
    //         ->when($params['state_id'], fn($q) => $q->where('state_id', $params['state_id']))
    //         ->pluck('id');

    //     // 2) pull polls with all your filters
    //     $polls = Poll::with([
    //         'candidate' => fn($q) => $q->withPivot('result_percentage'),
    //         'race',
    //         'pollster',
    //     ])
    //         ->whereIn('race_id', $raceIds)
    //         ->when(
    //             $params['pollster_id'],
    //             fn($q) =>
    //             $q->where('pollster_id', $params['pollster_id'])
    //         )
    //         ->when(
    //             $params['timeframe'],
    //             fn($q) =>
    //             $q->where('poll_date', '>=', now()->subDays($params['timeframe']))
    //         )
    //         ->orderBy('poll_date', 'desc')
    //         ->get();

    //     // 3) map into your exact JSON shape
    //     $result = $polls->map(fn($poll) => [
    //         'race'          => ucfirst($poll->race->race_type),
    //         'election_round' => ucfirst($poll->race->election_round),
    //         'pollster'      => $poll->pollster->name ?? 'N/A',
    //         'date'          => $poll->poll_date->format('Y-m-d'),
    //         'dateFormatted' => $poll->poll_date->format('D, j M'),
    //         'candidate' => (function () use ($poll) {
    //             $sorted = $poll->candidate
    //                 ->sortByDesc(fn($c) => $c->pivot->result_percentage);

    //             $top    = $sorted->first();
    //             $second = $sorted->skip(1)->first();

    //             $spread = round(
    //                 ($top->pivot->result_percentage ?? 0)
    //                     - ($second->pivot->result_percentage ?? 0),
    //                 1
    //             );

    //             return $top
    //                 ? "{$top->name} " . ($spread >= 0 ? '+' : '') . "{$spread}%"
    //                 : 'N/A';
    //         })(),
    //         'race_id'       => $poll->race_id,
    //     ]);
    //     return response()->json($result);
    // }


    public function filterPolls(Request $request)
    {
        $params = $request->validate([
            'pollType'    => 'nullable|string',
            'state_id'    => 'nullable|integer',
            'pollster_id' => 'nullable|integer',
            'timeframe'   => 'nullable|integer',
        ]);

        $pollType   = strtolower($params['pollType'] ?? '');
        $stateId    = $params['state_id'] ?? null;
        $pollsterId = $params['pollster_id'] ?? null;
        $timeframe  = $params['timeframe'] ?? 90;

        // Case 1: Initial load → no pollType, no state, no pollster
        $isInitialLoad = empty($pollType) && is_null($stateId) && is_null($pollsterId);

        if ($isInitialLoad) {
            // Default: get all races where race = election and created_at in last 90 days
            $raceIds = Race::where('race', 'election')
                ->where('created_at', '>=', now()->subDays($timeframe))
                ->pluck('id');
        } else {
            // User-driven: use pollType and filters
            $raceIds = Race::where('race_type', $pollType)
                ->when($stateId, fn($q) => $q->where('state_id', $stateId))
                ->pluck('id');
        }

        $polls = Poll::with([
            'candidate' => fn($q) => $q->withPivot('result_percentage'),
            'race',
            'pollster',
        ])
            ->whereIn('race_id', $raceIds)
            ->when($pollsterId, fn($q) => $q->where('pollster_id', $pollsterId))
            ->where('poll_date', '>=', now()->subDays($timeframe))
            ->orderBy('poll_date', 'desc')
            ->get();

        $result = $polls->map(fn($poll) => [
            'race'           => ucfirst($poll->race->race_type),
            'election_round' => ucfirst($poll->race->election_round),
            'pollster'       => $poll->pollster->name ?? 'N/A',
            'date'           => $poll->poll_date->format('Y-m-d'),
            'dateFormatted'  => $poll->poll_date->format('D, j M'),
            'candidate'      => (function () use ($poll) {
                $sorted = $poll->candidate
                    ->sortByDesc(fn($c) => $c->pivot->result_percentage);
                $top    = $sorted->first();
                $second = $sorted->skip(1)->first();
                $spread = round(
                    ($top->pivot->result_percentage ?? 0)
                        - ($second->pivot->result_percentage ?? 0),
                    1
                );
                return $top
                    ? "{$top->name} " . ($spread >= 0 ? '+' : '') . "{$spread}%"
                    : 'N/A';
            })(),
            'race_id' => $poll->race_id,
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
}
