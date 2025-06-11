<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_type',
        'race_type',
        'election_round',
        'state_id',
        'status',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }


    public function pollCandidates()
    {
        return $this->hasMany(PollCandidate::class, 'poll_id', 'id');
    }

    /**
     * Convenience: the actual Candidate models via the pivot table.
     */
    public function candidates()
    {
        return $this->belongsToMany(
            Candidate::class,
            'poll_candidates',
            'poll_id',
            'candidate_id'
        );
    }


    public function electionPoll()
    {
        return $this->hasOne(ElectionPoll::class, 'poll_id', 'id');
    }


    public function pollApproval()
    {
        return $this->hasOne(PollApproval::class);
    }
}
