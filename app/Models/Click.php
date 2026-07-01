<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $fillable = [
        'tracking_link_id', 'user_id', 'ip_address',
        'country_code', 'country_name', 'city',
        'os', 'os_version', 'device_type', 'browser',
        'user_agent', 'fingerprint', 'referrer', 'redirect_url',
        'is_fraud', 'fraud_reason', 'is_vpn', 'is_proxy',
        'is_counted', 'is_windows', 'is_mac', 'click_value',
    ];

    protected $casts = [
        'is_fraud' => 'boolean',
        'is_vpn' => 'boolean',
        'is_proxy' => 'boolean',
        'is_counted' => 'boolean',
        'is_windows' => 'boolean',
        'is_mac' => 'boolean',
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
