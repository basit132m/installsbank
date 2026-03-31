<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $fillable = [
        'tracking_link_id', 'user_id', 'ip_address',
        'country_code', 'country_name', 'city',
        'os', 'os_version', 'device_type', 'browser',
        'user_agent', 'fingerprint', 'referrer',
        'is_fraud', 'fraud_reason', 'is_vpn', 'is_proxy',
        'is_counted', 'is_windows', 'click_value',
    ];

    protected $casts = [
        'is_fraud' => 'boolean',
        'is_vpn' => 'boolean',
        'is_proxy' => 'boolean',
        'is_counted' => 'boolean',
        'is_windows' => 'boolean',
    ];

    public function trackingLink()
    {
        return $this->belongsTo(TrackingLink::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
