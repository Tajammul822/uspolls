<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectionPoll extends Model
{

    use HasFactory;

    protected $fillable = [
        'poll_id',
        'poll_date',
        'pollster_source',
        'sample_size',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'id');
    }

   
    public function results()
    {
        return $this->hasMany(ElectionPollResult::class, 'election_poll_id', 'id');
    }
}
