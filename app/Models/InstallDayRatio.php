<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallDayRatio extends Model
{
    protected $fillable = [
        'weekday',
        'ratio',
    ];
}
