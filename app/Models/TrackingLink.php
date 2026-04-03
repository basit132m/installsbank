<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrackingLink extends Model
{
    protected $fillable = [
        'user_id', 'name', 'original_url', 'unique_code',
        'is_active', 'total_clicks', 'unique_clicks', 'fraud_clicks', 'last_click_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_click_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getTrackingUrlAttribute(): string
    {
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
