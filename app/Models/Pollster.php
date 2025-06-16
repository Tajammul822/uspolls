<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pollster extends Model
{

     use HasFactory;

    protected $fillable = [
        'name',
        'rank',
        'description',
        'website',
    ];

     public function polls()
    {
        return $this->hasMany(Poll::class);
    }

     public function raceApprovals()
    {
        return $this->hasMany(RaceApproval::class);
    }
}
