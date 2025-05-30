<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'race_id',
        'title',
        'pollster_id',
        'state_id',
        'field_date_start',
        'field_date_end',
        'release_date',
        'sample_size',
        'margin_of_error',
        'source_url',
        'tags',
    ];

}
