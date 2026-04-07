<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallCountryRate extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'rate_usd',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rate_usd'  => 'decimal:6',
    ];
}
