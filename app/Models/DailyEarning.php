<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyEarning extends Model
{
    protected $fillable = [
        'user_id', 'date',
        'total_raw_clicks', 'windows_clicks', 'windows_clicks_divided',
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
