@extends('layouts.publisher')
@section('title', 'Ad Code')
@section('page-title', 'Ad Code')

@section('content')
<!-- Domain Restriction Notice -->
@php $website = auth()->user()->website; @endphp
<div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1.5px solid #fcd34d;border-radius:14px;padding:18px 20px;margin-bottom:24px;display:flex;gap:14px;align-items:flex-start;">
    <div style="width:40px;height:40px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
    </div>
    <div>
        <div style="font-size:14px;font-weight:700;color:#92400e;margin-bottom:4px;">Ad Code Domain Restriction</div>
        <div style="font-size:13px;color:#78350f;line-height:1.7;">
            Your ad code is <strong>locked to your registered website only</strong>.
            @if($website)
                Clicks will only be counted when visitors come from <strong style="color:#065f46;">{{ parse_url($website, PHP_URL_HOST) ?: $website }}</strong>.
                Clicks from any other domain will not be tracked or paid.
            @else
                Please update your profile with your website URL so your clicks can be validated.
            @endif
        </div>
    </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fff1f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#991b1b;font-weight:600;">
    {{ session('error') }}
</div>
@endif

@if(!$hasContract)
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:20px 24px;margin-bottom:24px;display:flex;gap:14px;align-items:flex-start;">
        <div style="width:40px;height:40px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="20" height="20" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <div style="font-size:14px;font-weight:700;color:#92400e;margin-bottom:4px;">Contract Required</div>
            <div style="font-size:13px;color:#78350f;line-height:1.7;">
                Please <a href="{{ route('publisher.contracts') }}" style="color:#01BF63;font-weight:600;">select your contract type</a> before requesting an ad code.
            </div>
        </div>
    </div>
@elseif($trackingLinks->isEmpty())
    {{-- No tracking links yet — show request button or pending state --}}
    <div style="background:white;border:2px solid #e5e7eb;border-radius:16px;padding:40px 32px;text-align:center;margin-bottom:24px;">
        <div style="width:64px;height:64px;background:#f0fdf4;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg width="32" height="32" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        @if($profile?->adcode_requested_at)
            <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Ad Code Request Submitted</div>
            <p style="font-size:14px;color:#6b7280;max-width:420px;margin:0 auto 16px;line-height:1.7;">
                Your request was received on <strong>{{ $profile->adcode_requested_at->format('M d, Y \a\t g:i A') }}</strong>. Our team will assign your tracking link shortly.
            </p>
            <span style="display:inline-flex;align-items:center;gap:8px;background:#fef3c7;border:1px solid #fcd34d;border-radius:20px;padding:8px 18px;font-size:13px;font-weight:700;color:#92400e;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pending Admin Assignment
            </span>
        @else
            <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Request Your Ad Code</div>
            <p style="font-size:14px;color:#6b7280;max-width:420px;margin:0 auto 24px;line-height:1.7;">
                Click the button below to notify our team. We'll assign your tracking link and ad code as soon as possible.
            </p>
            <form method="POST" action="{{ route('publisher.adcode.request') }}" style="display:inline;">
                @csrf
                <button type="submit"
                    style="background:#01BF63;color:white;border:none;border-radius:12px;padding:14px 32px;font-size:15px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;">
                    <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Request Ad Code
                </button>
            </form>
        @endif
    </div>
@else
    <!-- Select Preset -->
    <div class="card mb-6">
        <div class="card-title mb-1">Configure Your Ad Button</div>
        <div class="card-subtitle mb-4">Choose a button style and get your embed code</div>

        <form method="POST" action="{{ route('publisher.adcode.select') }}">
            @csrf
            <div class="grid-2" style="margin-bottom:20px;">
                <div class="form-group">
                    <label class="form-label">Tracking Link</label>
                    <select name="tracking_link_id" class="form-control">
                        @foreach($trackingLinks as $link)
                            <option value="{{ $link->id }}">{{ $link->name ?: $link->unique_code }} — {{ number_format($link->unique_clicks) }} valid clicks</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Custom Button Text (Optional)</label>
                    <input type="text" name="custom_text" class="form-control" placeholder="e.g. Download Free Now" maxlength="50">
                </div>
            </div>

            @if($presets->isNotEmpty())
                <label class="form-label">Select Button Style</label>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:20px;">
                    @foreach($presets as $preset)
                    <label style="cursor:pointer;">
                        <input type="radio" name="ad_preset_id" value="{{ $preset->id }}" style="display:none;" class="preset-radio" {{ $loop->first ? 'checked' : '' }}>
                        <div class="preset-card" style="border:2px solid #e5e7eb;border-radius:12px;padding:16px;text-align:center;transition:all 0.15s;">
                            <div style="font-size:12px;color:#6b7280;margin-bottom:12px;">{{ $preset->name }}</div>
                            <a href="#" onclick="return false;" style="
                                display:inline-block;
                                background:{{ $preset->button_color }};
                                color:{{ $preset->button_text_color }};
                                padding:{{ ['small'=>'8px 16px','medium'=>'10px 24px','large'=>'12px 32px'][$preset->button_size] ?? '10px 24px' }};
                                border-radius:{{ ['rounded'=>'8px','square'=>'0','pill'=>'50px'][$preset->button_style] ?? '8px' }};
                                font-size:{{ ['small'=>'12px','medium'=>'14px','large'=>'15px'][$preset->button_size] ?? '14px' }};
                                font-weight:600;
                                text-decoration:none;
                                font-family:Arial,sans-serif;
                            ">{{ $preset->button_text }}</a>
                        </div>
                    </label>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning">No ad button presets available yet. Contact admin.</div>
            @endif

            <button type="submit" class="btn btn-primary">Save Configuration & Get Code</button>
        </form>
    </div>

    <!-- Generated Codes -->
    @if($adButtons->isNotEmpty())
    <div class="card">
        <div class="card-title mb-4">Your Ad Embed Codes</div>
        @foreach($adButtons as $btn)
        <div style="border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:16px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:600;">{{ $btn->trackingLink?->name ?: 'Tracking Link' }}</div>
                    <div style="font-size:12px;color:#9ca3af;font-family:monospace;">{{ $btn->trackingLink?->tracking_url }}</div>
                </div>
                <span class="badge badge-success">Active</span>
            </div>

            <div style="margin-bottom:12px;">
                <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;">Preview:</div>
                {!! $btn->getEmbedCode() !!}
            </div>

            <div>
                <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:8px;">Embed Code (paste on your website):</div>
                <div class="code-box" id="code-{{ $btn->id }}">{!! htmlspecialchars($btn->getEmbedCode()) !!}</div>
                <button onclick="copyCode('code-{{ $btn->id }}')" class="btn btn-ghost btn-sm" style="margin-top:8px;">Copy Code</button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
@endif
@endsection

@push('scripts')
<script>
// Highlight selected preset
document.querySelectorAll('.preset-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.preset-card').forEach(c => c.style.borderColor = '#e5e7eb');
        if (this.checked) {
            this.nextElementSibling.style.borderColor = '#01BF63';
            this.nextElementSibling.style.background = '#f0fdf7';
        }
    });
    if (radio.checked) {
        radio.nextElementSibling.style.borderColor = '#01BF63';
        radio.nextElementSibling.style.background = '#f0fdf7';
    }
});

function copyCode(id) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text).then(() => alert('Code copied!'));
}
</script>
@endpush
