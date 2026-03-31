<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerPermission extends Model
{
    protected $fillable = [
        'user_id',
        'can_manage_publishers', 'can_view_publishers',
        'can_manage_contracts', 'can_manage_rates',
        'can_manage_withdrawals', 'can_view_withdrawals',
        'can_manage_ad_presets', 'can_view_fraud_alerts',
        'can_resolve_fraud_alerts', 'can_manage_support',
        'can_view_stats', 'can_manage_test_periods',
    ];

    protected $casts = [
        'can_manage_publishers' => 'boolean',
        'can_view_publishers' => 'boolean',
        'can_manage_contracts' => 'boolean',
        'can_manage_rates' => 'boolean',
        'can_manage_withdrawals' => 'boolean',
        'can_view_withdrawals' => 'boolean',
        'can_manage_ad_presets' => 'boolean',
        'can_view_fraud_alerts' => 'boolean',
        'can_resolve_fraud_alerts' => 'boolean',
        'can_manage_support' => 'boolean',
        'can_view_stats' => 'boolean',
        'can_manage_test_periods' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
