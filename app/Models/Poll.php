<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'race_id',
        'poll_date',
        'pollster_source',
        'sample_size',
    ];

    protected $casts = [
        'poll_date' => 'datetime',
    ];

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function results()
    {
        return $this->hasMany(PollResult::class);
    }

    public function candidates()
    {
        return $this->belongsToMany(
            Candidate::class,
            'race_candidates',   // pivot table name
            'race_id',           // FK on pivot that points to Poll->id
            'candidate_id'       // FK on pivot that points to Candidate->id
        );
    }
    // use HasFactory;

    // protected $fillable = [
    //     'poll_id',
    //     'poll_date',
    //     'pollster_source',
    //     'sample_size',
    // ];

    // public function poll()
    // {
    //     return $this->belongsTo(Poll::class, 'poll_id', 'id');
    // }


    // public function results()
    // {
    //     return $this->hasMany(ElectionPollResult::class, 'election_poll_id', 'id');
    // }

}
