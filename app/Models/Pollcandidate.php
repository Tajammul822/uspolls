<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PollCandidate extends Model
{
    use HasFactory;
    protected $table = 'poll_candidates';
    protected $fillable = [
        'poll_id',
        'candidate_id',
        'support_percentage',
    ];


    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
