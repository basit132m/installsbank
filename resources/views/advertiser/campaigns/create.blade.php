@extends('layouts.advertiser')
@section('title', 'New Campaign')
@section('page-title', 'Create Campaign')

@section('content')
@if($errors->any())
<div class="alert alert-danger mb-6">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
@endif

<div style="max-width:680px;">
    <!-- Info box -->
    <div style="background:#eff6ff;border:1.5px solid #93c5fd;border-radius:14px;padding:18px 20px;margin-bottom:28px;">
        <div style="font-size:14px;font-weight:700;color:#1e40af;margin-bottom:8px;">How It Works</div>
        <div style="font-size:13px;color:#1e3a8a;line-height:1.8;">
            1. Submit your campaign request with the destination URL and target clicks.<br>
            2. Admin reviews and sets the rate for your campaign.<br>
            3. You pay <strong>50% advance</strong> of the total campaign value.<br>
            4. Campaign goes live — clicks are tracked in real time.<br>
            5. Campaign completes when target clicks are reached.
        </div>
    </div>

    <div class="card">
        <div class="card-title mb-1">Campaign Details</div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:24px;">Fill in your campaign details. Admin will review and set pricing.</div>

        <form method="POST" action="{{ route('advertiser.campaigns.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Campaign Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Android App Install Campaign Q2">
            </div>

            <div class="form-group">
                <label class="form-label">Destination URL *</label>
                <input type="url" name="destination_url" class="form-control" value="{{ old('destination_url') }}" required placeholder="https://play.google.com/store/apps/details?id=...">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">The URL where users will be redirected (your app store page, landing page, etc.)</div>
            </div>

            <div class="form-group">
                <label class="form-label">Contract Type *</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:6px;">
                    <label style="cursor:pointer;">
                        <input type="radio" name="contract_type" value="per_click" {{ old('contract_type','per_click')==='per_click'?'checked':'' }} style="display:none;" class="contract-radio">
                        <div class="contract-card" style="border:2px solid #e5e7eb;border-radius:12px;padding:16px;transition:all 0.15s;">
                            <div style="font-size:14px;font-weight:700;margin-bottom:4px;">Per Click by Country</div>
                            <div style="font-size:12px;color:#6b7280;">Different rate for each country. Tier 1 (US/UK/CA/AU) costs more than Tier 3 traffic.</div>
                        </div>
                    </label>
                    <label style="cursor:pointer;">
                        <input type="radio" name="contract_type" value="fixed_rate" {{ old('contract_type')==='fixed_rate'?'checked':'' }} style="display:none;" class="contract-radio">
                        <div class="contract-card" style="border:2px solid #e5e7eb;border-radius:12px;padding:16px;transition:all 0.15s;">
                            <div style="font-size:14px;font-weight:700;margin-bottom:4px;">Fixed Rate (Mixed Traffic)</div>
                            <div style="font-size:12px;color:#6b7280;">Same rate regardless of country. Suitable for apps that accept global traffic.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Target Unique Clicks *</label>
                <input type="number" name="target_clicks" class="form-control" value="{{ old('target_clicks') }}" required min="100" step="100" placeholder="e.g. 5000">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Minimum 100 clicks. Campaign stops automatically when target is reached.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Additional Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Geo requirements, OS preferences, any special instructions for admin...">{{ old('notes') }}</textarea>
            </div>

            <div style="background:#f9fafb;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#6b7280;line-height:1.7;">
                ℹ After submission, admin will set the rate per click for your campaign. You will then be required to pay <strong>50% of the total campaign value in advance</strong> before the campaign goes live.
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">Submit Campaign Request</button>
                <a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.contract-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.contract-card').forEach(c => { c.style.borderColor='#e5e7eb'; c.style.background='white'; });
        if(this.checked){ this.nextElementSibling.style.borderColor='#3b82f6'; this.nextElementSibling.style.background='#eff6ff'; }
    });
    if(radio.checked){ radio.nextElementSibling.style.borderColor='#3b82f6'; radio.nextElementSibling.style.background='#eff6ff'; }
});
</script>
@endpush
@endsection
