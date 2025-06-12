<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectionPollResult extends Model
{

    use HasFactory;

    protected $fillable = [
        'election_poll_id',
        'candidate_id',
        'result_percentage',
    ];

    public function electionPoll()
    {
        return $this->belongsTo(ElectionPoll::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }


    
    // protected $fillable = [
    //     'election_poll_id',
    //     'candidate_id',
    //     'result_percentage',
    // ];

    // public function electionPoll()
    // {
    //     return $this->belongsTo(ElectionPoll::class, 'election_poll_id');
    // }

    // public function candidate()
    // {
    //     return $this->belongsTo(Candidate::class, 'candidate_id');
    // }



}
