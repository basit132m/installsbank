<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserCountryRate extends Model
{
    protected $fillable = ['country_code', 'country_name', 'rate_usd', 'is_active'];

    protected $casts = [
        'rate_usd'  => 'decimal:6',
        'is_active' => 'boolean',
    ];
}
