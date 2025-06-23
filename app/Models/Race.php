<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Race extends Model
{
    use HasFactory;

    protected $fillable = [
        'race',
        'race_type',
        'election_round',
        'state_id',
        'status',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }


    public function raceCandidates()
    {
        return $this->hasMany(RaceCandidate::class, 'race_id', 'id');
    }

    /**
     * Convenience: the actual Candidate models via the pivot table.
     */
    public function candidates()
    {
        return $this->belongsToMany(
            Candidate::class,
            'race_candidates',
            'race_id',
            'candidate_id'
        );
    }


    public function poll()
    {
        return $this->hasOne(Poll::class, 'race_id', 'id');
    }


    public function raceApproval()
    {
        return $this->hasMany(RaceApproval::class);
    }

    
}
