<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RaceCandidate extends Model
{
    use HasFactory;
    protected $table = 'race_candidates';
    protected $fillable = [
        'race_id',
        'candidate_id',
        'support_percentage',
    ];


    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
