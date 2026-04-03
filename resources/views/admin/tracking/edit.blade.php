@extends('layouts.admin')
@section('title', 'Edit Tracking Link')
@section('page-title', 'Edit Tracking Link')

@section('content')
<div style="max-width:560px;">
    <div class="card">
        <div class="card-title mb-1">Edit Tracking Link</div>
        <div class="card-subtitle mb-4">
            Code: <code style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:13px;">{{ $trackingLink->unique_code }}</code>
            &nbsp;·&nbsp; Publisher: <strong>{{ $trackingLink->user->name }}</strong>
        </div>

        <form method="POST" action="{{ route('admin.tracking.update', $trackingLink) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Link Name (Optional)</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $trackingLink->name) }}" placeholder="e.g. Main Campaign">
            </div>

            <div class="form-group">
                <label class="form-label">Destination URL</label>
                <input type="url" name="original_url" class="form-control" value="{{ old('original_url', $trackingLink->original_url) }}" required placeholder="https://example.com/download">
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">This is where visitors get redirected when they click the tracking link.</div>
            </div>

            <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:12px;margin-bottom:16px;">
                <div style="font-size:13px;font-weight:600;color:#92400e;margin-bottom:4px;">⚠ Note</div>
                <div style="font-size:13px;color:#92400e;">Changing the destination URL takes effect immediately. All existing tracking codes will redirect to the new URL.</div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admin.tracking.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
