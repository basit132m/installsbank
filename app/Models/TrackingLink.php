<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrackingLink extends Model
{
    protected $fillable = [
        'user_id', 'tracking_domain_id', 'name', 'original_url', 'unique_code',
        'url_windows', 'url_android', 'url_mac', 'url_other',
        'is_active', 'total_clicks', 'unique_clicks', 'fraud_clicks', 'last_click_at',
        'allowed_domain', 'campaign_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_click_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function trackingDomain()
    {
        return $this->belongsTo(TrackingDomain::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function adButton()
    {
        return $this->hasOne(AdButton::class);
    }

    public function fraudAlerts()
    {
        return $this->hasMany(FraudAlert::class);
    }

    /**
     * Resolve the destination URL based on detected OS.
     * Falls back to original_url if no device-specific URL is set.
     */
    public function resolveUrlForOs(string $os): string
    {
        $os = strtolower($os);

        if (str_contains($os, 'windows') && $this->url_windows) {
            return $this->url_windows;
        }
        if (str_contains($os, 'android') && $this->url_android) {
            return $this->url_android;
        }
        if ((str_contains($os, 'mac') || str_contains($os, 'ios') || str_contains($os, 'iphone') || str_contains($os, 'ipad')) && $this->url_mac) {
            return $this->url_mac;
        }
        if ($this->url_other) {
            return $this->url_other;
        }

        return $this->original_url;
    }

    public function getTrackingUrlAttribute(): string
    {
        if ($this->trackingDomain && $this->trackingDomain->is_active) {
            return $this->trackingDomain->base_url . '/track/' . $this->unique_code;
        }
        return url('/track/' . $this->unique_code);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->unique_code) {
                do {
                    $code = Str::upper(Str::random(3)) . rand(1000, 9999) . Str::upper(Str::random(3));
                } while (static::where('unique_code', $code)->exists());
                $model->unique_code = $code;
            }
        });
    }
}
