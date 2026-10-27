@extends('layouts.advertiser')
@section('title', 'New Campaign')
@section('page-title', 'Create Campaign')

@section('content')
@if($errors->any())
<div class="alert alert-danger mb-6">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;margin-bottom:20px;padding:12px 16px;border-radius:8px;">{{ session('error') }}</div>
@endif

<div style="max-width:680px;">
    <div style="background:#eff6ff;border:1.5px solid #93c5fd;border-radius:14px;padding:18px 20px;margin-bottom:28px;">
        <div style="font-size:14px;font-weight:700;color:#1e40af;margin-bottom:8px;">How It Works</div>
        <div style="font-size:13px;color:#1e3a8a;line-height:1.8;">
            1. Enter your campaign name, upload your Windows <strong>.exe</strong> and (optionally) your <strong>.ico</strong> icon.<br>
            2. Choose how many installs you want and your offered rate per install.<br>
            3. Submit — our team reviews it. We may propose a rate; you approve it.<br>
            4. Once approved, we make it live and deliver <strong>exactly</strong> the number of installs you set — no more.<br>
            5. Track installs (region, device, IP) in your dashboard, and increase the target anytime.
        </div>
    </div>

    <div class="card">
        <div class="card-title mb-1">Campaign Details</div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:24px;">Upload your software and set your install target. Our team will review and confirm pricing.</div>

        <form method="POST" action="{{ route('advertiser.campaigns.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Campaign Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. FontManager Pro — Windows">
            </div>

            <div class="form-group">
                <label class="form-label">Software Installer (.exe) *</label>
                <input type="file" name="installer" accept=".exe" class="form-control" required>
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Your real Windows setup file. Max 200 MB. We never modify it — users receive it exactly as you uploaded it.</div>
            </div>

            <div class="form-group">
                <label class="form-label">App Icon (.ico, optional)</label>
                <input type="file" name="icon" accept=".ico" class="form-control">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Your software's icon as a Windows <code>.ico</code> file — used as the icon of the download so it shows your brand.</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="sw-grid">
                <div class="form-group">
                    <label class="form-label">Number of Installs *</label>
                    <input type="number" name="max_installs" class="form-control" value="{{ old('max_installs') }}" min="1" required placeholder="e.g. 100">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">We deliver exactly this many, then stop. You can increase it anytime.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Offered Rate (per install) *</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;">$</span>
                        <input type="number" name="offered_rate" class="form-control" value="{{ old('offered_rate') }}" min="0" step="0.01" required placeholder="0.40" style="padding-left:24px;">
                    </div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">What you're offering to pay for each install.</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Target Countries (Geo) *</label>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;margin-top:6px;">
                    @foreach($geoModes as $val => $label)
                    <label style="border:2px solid #e5e7eb;border-radius:10px;padding:12px;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:13px;">
                        <input type="radio" name="geo_mode" value="{{ $val }}" {{ old('geo_mode','mixed')===$val?'checked':'' }}>
                        <span><strong>{{ $label }}</strong></span>
                    </label>
                    @endforeach
                </div>
                <div style="font-size:11px;color:#9ca3af;margin-top:6px;">We only send installs from your chosen tier. Off-target visitors are redirected away — never counted.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Exclude specific countries (optional)</label>
                <select name="excluded_countries[]" multiple size="8" class="form-control" style="height:auto;">
                    @foreach($countryGroups as $group => $list)
                    <optgroup label="{{ $group }}">
                        @foreach($list as $code => $cname)
                        <option value="{{ $code }}" {{ in_array($code, old('excluded_countries', [])) ? 'selected' : '' }}>{{ $cname }} ({{ $code }})</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Hold Ctrl/Cmd to pick several. Example: choose "Tier 1 only" above and exclude Canada here to get Tier 1 without Canada.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Additional Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Anything our team should know about this software">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Campaign</button>
            <a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost" style="margin-left:8px;">Cancel</a>
        </form>
    </div>
</div>
<style>@media(max-width:620px){.sw-grid{grid-template-columns:1fr !important;}}</style>
@endsection
