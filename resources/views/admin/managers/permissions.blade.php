@extends('layouts.admin')
@section('title', 'Permissions')
@section('page-title', $user->name . ' — Permissions')

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <form method="POST" action="{{ route('admin.managers.update-permissions', $user) }}">
            @csrf @method('PUT')
            @foreach(['can_manage_publishers'=>'Manage Publishers','can_view_publishers'=>'View Publishers','can_manage_contracts'=>'Manage Contracts','can_manage_rates'=>'Manage Rates','can_manage_withdrawals'=>'Manage Withdrawals','can_view_withdrawals'=>'View Withdrawals','can_manage_ad_presets'=>'Manage Ad Presets','can_view_fraud_alerts'=>'View Fraud Alerts','can_resolve_fraud_alerts'=>'Resolve Fraud Alerts','can_manage_support'=>'Manage Support','can_view_stats'=>'View Stats','can_manage_test_periods'=>'Manage Test Periods'] as $key => $label)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;">
                <span style="font-size:14px;font-weight:500;">{{ $label }}</span>
                <label class="toggle"><input type="checkbox" name="{{ $key }}" value="1" {{ $permissions->$key ?? false ? 'checked' : '' }}><span class="toggle-slider"></span></label>
            </div>
            @endforeach
            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Save Permissions</button>
                <a href="{{ route('admin.managers.index') }}" class="btn btn-ghost">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
