<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pollcandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'candidate_id',
        'support_percentage',
    ];


}
