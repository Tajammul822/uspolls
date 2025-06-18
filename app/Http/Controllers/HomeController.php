<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Poll;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{



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
    //  /**
    //  * Live‐search by candidate name → return distinct polls,
    //  * each labeled “A vs B vs …” for the dropdown.
    //  */
    // public function searchPolls(Request $request)
    // {
    //     $q = $request->input('search', '');
    //     // 1) find poll_ids where any candidate name matches
    //     $pollIds = Candidate::where('name', 'like', "%{$q}%")
    //         ->join('poll_results', 'candidates.id', '=', 'poll_results.candidate_id')
    //         ->distinct()
    //         ->pluck('poll_results.poll_id');

    //     // 2) for each poll, build “A vs B vs C”
    //     $results = Poll::whereIn('id', $pollIds)
    //         ->with('candidates')
    //         ->get()
    //         ->map(function ($poll) {
    //             $names = $poll->candidates->pluck('name')->toArray();
    //             return [
    //                 'poll_id' => $poll->id,
    //                 'label'   => implode(' vs ', $names),
    //             ];
    //         });

    //     return Response::json($results);
    // }

    // /**
    //  * Given a poll_id, return:
    //  * – pollster_id
    //  * – poll_date
    //  * – sample_size
    //  * – array of { name, pct } for each candidate
    //  */
    // public function getResults(Poll $poll)
    // {
    //     $poll->load('candidate');

    //     $data = [
    //         'pollster_id' => $poll->pollster_id,
    //         'poll_date'   => $poll->poll_date,
    //         'sample_size' => $poll->sample_size,
    //         'results'     => $poll->candidate->map(function ($cand) {
    //             return [
    //                 'name' => $cand->name,
    //                 'pct'  => $cand->pivot->result_percentage,
    //             ];
    //         })->values()->toArray(),
    //     ];

    //     return Response::json($data);
    // }
}
