<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Poll;
use App\Models\Race;
use App\Models\State;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    // Show the page with your State dropdown
    public function index()
    {
        $states = State::orderBy('name')->get(['id','name']);
        return view('frontend.home', compact('states'));
    }

    // AJAX: Fetch all polls for any race in this state
    public function pollsByState($stateId)
    {
        $polls = Poll::whereHas('race', function($q) use ($stateId){
                $q->where('state_id', $stateId);
            })
            ->with(['candidate','pollster'])
            ->get();

        // Build JSON-friendly array
        $data = $polls->map(function($poll){
            // assume exactly two candidates per poll
            $c = $poll->candidate->map(fn($cand)=>[
                'name' => $cand->name,
                'pct'  => $cand->pivot->result_percentage
            ])->values();

            // sort descending so [0] is winner
            $c = $c->sortByDesc('pct')->values();

            $net = number_format($c[0]['pct'] - ($c[1]['pct'] ?? 0), 1);

            return [
                'pollster' => $poll->pollster->name ?? 'N/A',
                'date'     => $poll->poll_date,
                'sample'   => $poll->sample_size,
                'cand1'    => $c[0]['pct'],
                'cand2'    => $c[1]['pct'] ?? 0,
                'net'      => $net,
                'leadClass'=> $net >= 0 ? 'positive' : 'negative',
                'c1class'  => 'positive',   // winner always positive
                'c2class'  => 'negative',   // runner-up negative
            ];
        });

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
                'poll_date'     => $poll->poll_date,
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
