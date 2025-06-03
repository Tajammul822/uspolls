<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pollapproval extends Model
{
    use HasFactory;
    protected $table = 'poll_approvals';
    protected $fillable = [
        'poll_id',
        'approve_percentage',
        'disapprove_percentage',
        'neutral_percentage',
        'subject',
    ];


    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
