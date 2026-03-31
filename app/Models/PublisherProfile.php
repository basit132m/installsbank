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
    ];

    protected $casts = [
        'test_started_at' => 'datetime',
        'test_ended_at' => 'datetime',
        'payment_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
