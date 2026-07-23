<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PortalAccount extends Authenticatable
{
    protected $fillable = [
        'username', 'password', 'display_title', 'tracking_link_id',
        'divider_value', 'divider_enabled', 'min_clicks', 'max_clicks',
        'is_active', 'last_login_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'divider_value'   => 'decimal:2',
        'divider_enabled' => 'boolean',
        'min_clicks'      => 'integer',
        'max_clicks'      => 'integer',
        'is_active'       => 'boolean',
        'last_login_at'   => 'datetime',
    ];

    public function trackingLink()
    {
        return $this->belongsTo(TrackingLink::class);
    }

    public function dailyStats()
    {
        return $this->hasMany(PortalDailyStat::class);
    }

    /** Effective divider (>= 1). */
    public function effectiveDivider(): float
    {
        return $this->divider_enabled ? max(1, (float) $this->divider_value) : 1;
    }
}
