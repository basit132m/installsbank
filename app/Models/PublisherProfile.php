<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherProfile extends Model
{
    protected $fillable = [
        'user_id', 'balance', 'total_earnings', 'total_withdrawn',
        'pending_balance', 'payment_address', 'payment_network',
        'contract_type', 'fixed_daily_rate', 'last_fixed_credit_date', 'payment_enabled',
        'test_total_clicks', 'test_started_at', 'test_ended_at',
        'test_status', 'test_payout_eligible', 'notes',
        'fraud_country_mismatch', 'fraud_suspicious_referrer',
        'fraud_headless_browser', 'allowed_countries',
        'install_pending_clicks',
        'adcode_requested_at',
    ];

    protected $casts = [
        'last_fixed_credit_date'   => 'date',
        'test_started_at'          => 'datetime',
        'test_ended_at'            => 'datetime',
        'payment_enabled'          => 'boolean',
        'test_payout_eligible'     => 'boolean',
        'fraud_country_mismatch'   => 'boolean',
        'fraud_suspicious_referrer'=> 'boolean',
        'fraud_headless_browser'   => 'boolean',
        'allowed_countries'        => 'array',
        'install_pending_clicks'   => 'array',
        'adcode_requested_at'      => 'datetime',
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
