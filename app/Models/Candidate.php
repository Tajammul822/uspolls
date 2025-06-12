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
        'status',
    ];
    
    public function pollCandidates()
    {
        return $this->hasMany(PollCandidate::class);
    }


}
