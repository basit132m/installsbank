<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MegaUrl extends Model
{
    protected $fillable = ['nickname', 'url', 'notes'];
}
