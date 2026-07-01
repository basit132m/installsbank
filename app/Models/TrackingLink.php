<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrackingLink extends Model
{
    protected $fillable = [
        'user_id', 'tracking_domain_id', 'name', 'original_url', 'unique_code',
        'url_windows', 'url_android', 'url_mac', 'url_other',
        'windows_schedule_enabled', 'windows_schedules',
        'is_active', 'total_clicks', 'unique_clicks', 'fraud_clicks', 'last_click_at',
        'allowed_domain', 'campaign_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_click_at' => 'datetime',
        'windows_schedule_enabled' => 'boolean',
        'windows_schedules' => 'array',
    ];

    /** All schedule times are entered and evaluated in Pakistan time */
    public const SCHEDULE_TIMEZONE = 'Asia/Karachi';

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
     * Windows URL honoring the auto-redirect timer.
     * When the timer is on and the current Pakistan time falls inside a slot,
     * that slot's URL wins; otherwise the normal url_windows applies.
     */
    public function resolveWindowsUrl(): ?string
    {
        if ($this->windows_schedule_enabled && !empty($this->windows_schedules)) {
            $nowPk = now(self::SCHEDULE_TIMEZONE)->format('H:i');

            foreach ($this->windows_schedules as $slot) {
                $start = $slot['start'] ?? null;
                $end   = $slot['end'] ?? null;
                $url   = $slot['url'] ?? null;

                if (!$start || !$end || !$url || $start === $end) {
                    continue;
                }

                // Overnight window (e.g. 22:00 → 04:00) wraps past midnight
                $matches = $start < $end
                    ? ($nowPk >= $start && $nowPk < $end)
                    : ($nowPk >= $start || $nowPk < $end);

                if ($matches) {
                    return $url;
                }
            }
        }

        return $this->url_windows;
    }

    /**
     * Resolve the destination URL based on detected OS.
     * Falls back to original_url if no device-specific URL is set.
     */
    public function resolveUrlForOs(string $os): string
    {
        $os = strtolower($os);

        if (str_contains($os, 'windows')) {
            $windowsUrl = $this->resolveWindowsUrl();
            if ($windowsUrl) {
                return $windowsUrl;
            }
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
