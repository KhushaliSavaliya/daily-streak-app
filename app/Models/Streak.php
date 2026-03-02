<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Streak extends Model
{
    protected $fillable = [ 
        'count',
        'freezes_available',
        'last_commit_date',
    ];
}
