<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'user_id', 'name', 'destination_url', 'fallback_url',
        'contract_type', 'fixed_rate', 'country_rates',
        'target_clicks', 'delivered_clicks',
        'total_value', 'advance_amount', 'total_paid',
        'status', 'click_breakdown', 'admin_note',
        'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'country_rates'   => 'array',
            'click_breakdown' => 'array',
            'started_at'      => 'datetime',
            'completed_at'    => 'datetime',
            'total_value'     => 'decimal:4',
            'advance_amount'  => 'decimal:4',
            'total_paid'      => 'decimal:4',
            'fixed_rate'      => 'decimal:6',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(CampaignPayment::class);
    }

    public function trackingLinks()
    {
        return $this->hasMany(TrackingLink::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'cancelled']);
    }

    public function progressPercent(): int
    {
        if (!$this->target_clicks) return 0;
        return min(100, (int) round(($this->delivered_clicks / $this->target_clicks) * 100));
    }

    public function remainingClicks(): int
    {
        return max(0, $this->target_clicks - $this->delivered_clicks);
    }
}
