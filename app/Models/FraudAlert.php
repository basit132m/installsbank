<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FraudAlert extends Model
{
    protected $fillable = [
        'user_id', 'tracking_link_id', 'alert_type',
        'ip_address', 'country_code', 'details', 'occurrences',
        'is_resolved', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'details' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trackingLink()
    {
        return $this->belongsTo(TrackingLink::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
