<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerPermission extends Model
{
    protected $fillable = [
        'user_id',
        // Publishers
        'can_manage_publishers', 'can_view_publishers',
        'can_manage_test_periods', 'can_manage_publisher_websites',
        // Contracts
        'can_manage_contracts', 'can_manage_contract_requests',
        'can_manage_rate_increase_requests',
        // Rates & Tracking
        'can_manage_rates', 'can_manage_install_rates',
        'can_manage_tracking', 'can_manage_blacklisted_domains',
        // Financials
        'can_manage_withdrawals', 'can_view_withdrawals',
        'can_manage_ad_presets',
        // Advertisers
        'can_manage_advertisers', 'can_manage_campaigns',
        // Fraud & Support
        'can_view_fraud_alerts', 'can_resolve_fraud_alerts',
        'can_manage_support', 'can_manage_live_chat',
        // Stats
        'can_view_stats',
        // Communications
        'can_send_broadcast_emails',
    ];

    protected $casts = [
        'can_manage_publishers'           => 'boolean',
        'can_view_publishers'             => 'boolean',
        'can_manage_test_periods'         => 'boolean',
        'can_manage_publisher_websites'   => 'boolean',
        'can_manage_contracts'            => 'boolean',
        'can_manage_contract_requests'    => 'boolean',
        'can_manage_rate_increase_requests' => 'boolean',
        'can_manage_rates'                => 'boolean',
        'can_manage_install_rates'        => 'boolean',
        'can_manage_tracking'             => 'boolean',
        'can_manage_blacklisted_domains'  => 'boolean',
        'can_manage_withdrawals'          => 'boolean',
        'can_view_withdrawals'            => 'boolean',
        'can_manage_ad_presets'           => 'boolean',
        'can_manage_advertisers'          => 'boolean',
        'can_manage_campaigns'            => 'boolean',
        'can_view_fraud_alerts'           => 'boolean',
        'can_resolve_fraud_alerts'        => 'boolean',
        'can_manage_support'              => 'boolean',
        'can_manage_live_chat'            => 'boolean',
        'can_view_stats'                  => 'boolean',
        'can_send_broadcast_emails'       => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
