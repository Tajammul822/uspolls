<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PollResult extends Model
{

    use HasFactory;

    protected $fillable = [
        'poll_id',
        'candidate_id',
        'result_percentage',
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
