@extends('layouts.admin')
@section('title', 'Permissions')
@section('page-title', $user->name . ' — Permissions')

@section('content')
<div style="max-width:700px;">
    <div class="card">
        <form method="POST" action="{{ route('admin.managers.update-permissions', $user) }}">
            @csrf @method('PUT')

            @php
            $groups = [
                'Publishers' => [
                    'can_view_publishers'           => 'View Publishers',
                    'can_manage_publishers'         => 'Manage Publishers (activate, suspend, edit)',
                    'can_manage_test_periods'       => 'Manage Test Periods',
                    'can_manage_publisher_websites' => 'Manage Publisher Website Requests',
                ],
                'Contracts' => [
                    'can_manage_contracts'              => 'Manage Contracts (offer, expire)',
                    'can_manage_contract_requests'      => 'Manage Contract Change Requests',
                    'can_manage_rate_increase_requests' => 'Manage Rate Increase Requests',
                ],
                'Rates & Tracking' => [
                    'can_manage_rates'               => 'Manage Click Country Rates',
                    'can_manage_install_rates'       => 'Manage Install Country Rates',
                    'can_manage_tracking'            => 'Manage Tracking Links & Domains',
                    'can_manage_blacklisted_domains' => 'Manage Blacklisted Domains',
                    'can_manage_ad_presets'          => 'Manage Ad Presets',
                ],
                'Financials' => [
                    'can_view_withdrawals'   => 'View Withdrawals',
                    'can_manage_withdrawals' => 'Manage Withdrawals (approve, reject)',
                ],
                'Advertisers' => [
                    'can_manage_advertisers' => 'Manage Advertisers',
                    'can_manage_campaigns'   => 'Manage Campaigns',
                ],
                'Fraud & Support' => [
                    'can_view_fraud_alerts'    => 'View Fraud Alerts',
                    'can_resolve_fraud_alerts' => 'Resolve Fraud Alerts',
                    'can_manage_support'       => 'Manage Support Tickets',
                    'can_manage_live_chat'     => 'Manage Live Chat',
                ],
                'Stats' => [
                    'can_view_stats' => 'View Publisher Stats',
                ],
            ];
            @endphp

            @foreach($groups as $groupName => $items)
            <div style="margin-bottom:24px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid #f3f4f6;">
                    {{ $groupName }}
                </div>
                @foreach($items as $key => $label)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f9fafb;">
                    <span style="font-size:14px;font-weight:500;color:#374151;">{{ $label }}</span>
                    <label class="toggle">
                        <input type="checkbox" name="{{ $key }}" value="1" {{ $permissions->$key ?? false ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                @endforeach
            </div>
            @endforeach

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">Save Permissions</button>
                <a href="{{ route('admin.managers.index') }}" class="btn btn-ghost">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
