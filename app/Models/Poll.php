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
        'pollster_id',
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

    public function pollster()
    {
        return $this->belongsTo(Pollster::class);
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

    public function candidate()
    {
        return $this->belongsToMany(Candidate::class, 'poll_results')
            ->withPivot('result_percentage');
    }
}
