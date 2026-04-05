@extends('layouts.publisher')
@section('title', 'Ad Code')
@section('page-title', 'Ad Code')

@section('content')
@if(!$hasContract)
    <div class="alert alert-warning">
        You need an accepted contract before you can use ad codes. Please wait for admin to offer a contract.
    </div>
@endif

@if($trackingLinks->isEmpty())
    <div class="alert alert-info">
        No tracking links assigned yet. Please contact support or wait for admin to assign you a tracking link.
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
