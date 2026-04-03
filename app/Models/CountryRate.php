<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryRate extends Model
{
    protected $fillable = ['country_code', 'country_name', 'rate_per_click', 'is_active', 'needs_rate_update'];
    protected $casts = ['is_active' => 'boolean', 'needs_rate_update' => 'boolean'];
}
