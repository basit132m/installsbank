@extends('layouts.admin')
@section('title', 'Create Link')
@section('page-title', 'Create Install Link')

@section('content')
@if($errors->any())
<div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;margin-bottom:20px;">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
@endif

<a href="{{ route('admin.software-links.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">&larr; All links</a>

<div class="card" style="max-width:680px;">
    <div class="card-title" style="margin-bottom:6px;">New Install Link</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">
        Pick the advertiser's campaign and set a fallback URL. The link enforces that campaign's geo rules
        (set by the advertiser); visitors outside the target geo go to the fallback instead of the installer.
    </p>

    @if($campaigns->isEmpty())
        <div style="color:#9ca3af;font-size:14px;">No campaigns with an uploaded installer yet.</div>
    @else
    <form method="POST" action="{{ route('admin.software-links.store') }}">
        @csrf
        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">Campaign *</label>
            <select name="software_campaign_id" class="form-control" required>
                <option value="">— Select a campaign —</option>
                @foreach($campaigns as $c)
                <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->user?->name }} ({{ $c->geoModeLabel() }}@if(!empty($c->excluded_countries)), excl {{ implode(',', $c->excluded_countries) }}@endif)</option>
                @endforeach
            </select>
            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">The geo targeting shown comes from the advertiser's campaign settings.</div>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">Link name (optional)</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Fontiq — Tier 1 push">
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label">Fallback URL (optional)</label>
            <input type="url" name="fallback_url" class="form-control" value="{{ old('fallback_url') }}" placeholder="https://example.com/other-offer">
            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Where off-geo visitors are redirected (e.g. another offer). Leave blank to show a 404 to off-geo visitors.</div>
        </div>

        <button class="btn btn-primary">Create Link</button>
    </form>
    @endif
</div>
@endsection
