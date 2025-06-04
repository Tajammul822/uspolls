<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poll extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'race_id',
    //     'title',
    //     'pollster_id',
    //     'state_id',
    //     'field_date_start',
    //     'field_date_end',
    //     'release_date',
    //     'sample_size',
    //     'margin_of_error',
    //     'source_url',
    //     'tags',
    // ];

    protected $fillable = [
        'candidate_name',
        'party',
        'race',
        'support_percentage',
        'approval_rating',
        'pollster',
        'state_id',
        'field_date_start',
        'field_date_end',
        'release_date',
        'sample_size',
        'margin_of_error',
        'source_url',
        'tags',
        'status',
    ];


    protected $casts = [
        'field_date_start' => 'datetime',
        'field_date_end' => 'datetime',
        'release_date' => 'datetime',
        'status' => 'boolean',
    ];




    // public function race()
    // {
    //     return $this->belongsTo(Race::class);
    // }

    // public function pollster()
    // {
    //     return $this->belongsTo(Pollster::class);
    // }

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

    
}
