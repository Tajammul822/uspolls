<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PollApproval extends Model
{
    use HasFactory;
    protected $table = 'poll_approvals';
    protected $fillable = [
        'poll_id',
        'name',
        'poll_date',
        'pollster',
        'sample_size',
        'approve_rating',
        'disapprove_rating',
    ];

    protected $casts = [
        'poll_date' => 'datetime',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
