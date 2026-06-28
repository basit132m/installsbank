<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MacInstallCountryRate extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'mac_rate_usd',
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'mac_rate_usd' => 'decimal:6',
    ];
}
