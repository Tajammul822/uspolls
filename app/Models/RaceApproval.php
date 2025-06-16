<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RaceApproval extends Model
{
    use HasFactory;
    protected $table = 'race_approvals';
    protected $fillable = [
        'race_id',
        'name',
        'race_date',
        'pollster_id',
        'sample_size',
        'approve_rating',
        'disapprove_rating',
    ];

    protected $casts = [
        'race_date' => 'datetime',
    ];

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

      public function pollster()
    {
        return $this->belongsTo(Pollster::class);
    }
}
