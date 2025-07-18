<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'party',
        'image',
        'status',
    ];
    
    public function raceCandidates()
    {
        return $this->hasMany(RaceCandidate::class);
    }


}
