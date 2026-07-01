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
     * The current (or most recent) occurrence of a timer slot's window, as real
     * instants in the app timezone (UTC). Used to count clicks for only the
     * current activation — so the count resets to 0 each time the window restarts.
     *
     * Returns ['start' => Carbon, 'end' => Carbon, 'active' => bool] or null.
     */
    public function currentWindowRange(array $slot): ?array
    {
        $start = $slot['start'] ?? null;
        $end   = $slot['end'] ?? null;
        if (!$start || !$end || $start === $end) {
            return null;
        }

        $tz  = self::SCHEDULE_TIMEZONE;
        $now = \Illuminate\Support\Carbon::now($tz);
        [$sh, $sm] = array_map('intval', explode(':', $start));
        [$eh, $em] = array_map('intval', explode(':', $end));

        $startToday = $now->copy()->setTime($sh, $sm, 0);
        $endToday   = $now->copy()->setTime($eh, $em, 0);

        if ($start < $end) {
            // Same-day window
            if ($now->gte($startToday) && $now->lt($endToday)) {
                $occStart = $startToday;      $occEnd = $endToday;      $active = true;
            } elseif ($now->lt($startToday)) {
                $occStart = $startToday->copy()->subDay(); $occEnd = $endToday->copy()->subDay(); $active = false;
            } else {
                $occStart = $startToday;      $occEnd = $endToday;      $active = false;
            }
        } else {
            // Overnight window that crosses midnight
            if ($now->gte($startToday)) {
                $occStart = $startToday;                   $occEnd = $endToday->copy()->addDay(); $active = true;
            } elseif ($now->lt($endToday)) {
                $occStart = $startToday->copy()->subDay(); $occEnd = $endToday;                   $active = true;
            } else {
                $occStart = $startToday->copy()->subDay(); $occEnd = $endToday;                   $active = false;
            }
        }

        // Convert to UTC so created_at comparisons are correct
        return [
            'start'  => $occStart->utc(),
            'end'    => $occEnd->utc(),
            'active' => $active,
        ];
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
                // Per-slot on/off toggle (default on for older saved slots)
                if (($slot['enabled'] ?? true) === false) {
                    continue;
                }

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

                if (!$matches) {
                    continue;
                }

                // Per-slot click cap for the current window occurrence. Once reached,
                // this slot stops winning and traffic falls back to the next slot / default.
                $cap = (int) ($slot['cap'] ?? 0);
                if ($cap > 0 && $this->windowClickCount($slot) >= $cap) {
                    continue;
                }

                return $url;
            }
        }

        return $this->url_windows;
    }

    /**
     * Number of Windows clicks already sent to this slot's URL during its
     * current window occurrence (resets each time the window restarts).
     */
    public function windowClickCount(array $slot): int
    {
        $range = $this->currentWindowRange($slot);
        if (!$range || empty($slot['url'])) {
            return 0;
        }

        return Click::where('tracking_link_id', $this->id)
            ->where('is_windows', true)
            ->where('redirect_url', $slot['url'])
            ->where('created_at', '>=', $range['start'])
            ->where('created_at', '<', $range['end'])
            ->count();
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
