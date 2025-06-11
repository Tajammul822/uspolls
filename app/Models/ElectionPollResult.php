<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionPollResult extends Model
{
    protected $fillable = [
        'election_poll_id',
        'candidate_id',
        'result_percentage',
    ];

    public function electionPoll()
    {
        return $this->belongsTo(ElectionPoll::class, 'election_poll_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
}
