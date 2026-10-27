@extends('layouts.advertiser')
@section('title', 'Upload Software')
@section('page-title', 'Upload Software')

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
            1. Upload your Windows software installer (.exe).<br>
            2. Our team reviews it and activates your install campaign.<br>
            3. We distribute it and track every real install for you.<br>
            4. You see installs here in real time — region, device and IP, campaign-wise.
        </div>
    </div>

    <div class="card">
        <div class="card-title mb-1">Software Details</div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:24px;">Upload your installer. We'll review and activate it shortly.</div>

        <form method="POST" action="{{ route('advertiser.software.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Campaign Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. FontManager Pro — Windows">
            </div>

            <div class="form-group">
                <label class="form-label">Installer (.exe) *</label>
                <input type="file" name="installer" accept=".exe" class="form-control" required>
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Your real Windows setup file. Max 200 MB. We never modify it — it's delivered to users exactly as you uploaded it.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Anything our team should know about this software">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Upload &amp; Submit</button>
            <a href="{{ route('advertiser.software.index') }}" class="btn btn-ghost" style="margin-left:8px;">Cancel</a>
        </form>
    </div>
</div>
@endsection
