<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Streak extends Model
{
    protected $fillable = [ 
        'count',
        'best_streak',
        'freezes_available',
        'last_commit_date',
        'achievements',
    ];

    protected $casts = [
        'achievements' => 'array',
    ];
}
