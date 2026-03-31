@extends('layouts.admin')
@section('title', 'Add Manager')
@section('page-title', 'Add Manager')

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <form method="POST" action="{{ route('admin.managers.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="8"></div>
            <div class="card-title" style="margin:20px 0 12px;">Permissions</div>
            @foreach(['can_manage_publishers'=>'Manage Publishers','can_view_publishers'=>'View Publishers','can_manage_contracts'=>'Manage Contracts','can_manage_rates'=>'Manage Rates','can_manage_withdrawals'=>'Manage Withdrawals','can_view_withdrawals'=>'View Withdrawals','can_manage_ad_presets'=>'Manage Ad Presets','can_view_fraud_alerts'=>'View Fraud Alerts','can_resolve_fraud_alerts'=>'Resolve Fraud Alerts','can_manage_support'=>'Manage Support','can_view_stats'=>'View Stats','can_manage_test_periods'=>'Manage Test Periods'] as $key => $label)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f3f4f6;">
                <span style="font-size:14px;">{{ $label }}</span>
                <label class="toggle"><input type="checkbox" name="{{ $key }}" value="1"><span class="toggle-slider"></span></label>
            </div>
            @endforeach
            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Create Manager</button>
                <a href="{{ route('admin.managers.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
