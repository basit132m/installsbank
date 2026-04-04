<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherProfile extends Model
{
    protected $fillable = [
        'user_id', 'balance', 'total_earnings', 'total_withdrawn',
        'contract_type', 'fixed_daily_rate', 'payment_enabled',
        'test_total_clicks', 'test_started_at', 'test_ended_at',
        'test_status', 'notes',
        'fraud_country_mismatch', 'fraud_suspicious_referrer',
        'fraud_headless_browser', 'allowed_countries',
    ];

    protected $casts = [
        'test_started_at'          => 'datetime',
        'test_ended_at'            => 'datetime',
        'payment_enabled'          => 'boolean',
        'fraud_country_mismatch'   => 'boolean',
        'fraud_suspicious_referrer'=> 'boolean',
        'fraud_headless_browser'   => 'boolean',
        'allowed_countries'        => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fixed rate publishers are paid a fixed amount externally.
     * Per-click earnings should not be tracked or displayed for them.
     */
    public function isFixedRate(): bool
    {
        return $this->contract_type !== null && $this->contract_type !== 'per_click';
    }
}
