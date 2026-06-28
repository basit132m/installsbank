<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyEarning extends Model
{
    protected $fillable = [
        'user_id', 'date',
        'total_raw_clicks', 'windows_clicks',
        'windows_clicks_base_count', 'windows_clicks_base_divided',
        'windows_clicks_divided',
        'mac_clicks', 'mac_clicks_base_count', 'mac_clicks_base_divided', 'mac_clicks_divided',
        'valid_clicks', 'earnings', 'country_breakdown', 'os_breakdown',
    ];

    protected $casts = [
        'date' => 'date',
        'country_breakdown' => 'array',
        'os_breakdown' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
