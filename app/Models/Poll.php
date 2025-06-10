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
        'status',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // public function candidates()
    // {
    //     return $this->hasMany(PollCandidate::class);
    // }

    // public function approvals()
    // {
    //     return $this->hasMany(PollApproval::class);
    // }


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
    
}
