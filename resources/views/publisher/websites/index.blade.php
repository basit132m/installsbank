@extends('layouts.publisher')
@section('title', 'My Websites')
@section('page-title', 'My Websites')

@section('content')
@if(session('success'))
<div class="alert" style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;margin-bottom:20px;">✓ {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;margin-bottom:20px;">
    @foreach($errors->all() as $error)<div>✗ {{ $error }}</div>@endforeach
</div>
@endif

<!-- Info banner -->
<div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid #93c5fd;border-radius:14px;padding:18px 20px;margin-bottom:24px;display:flex;gap:14px;align-items:flex-start;">
    <div style="width:40px;height:40px;background:#dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
    </div>
    <div>
        <div style="font-size:14px;font-weight:700;color:#1e40af;margin-bottom:4px;">Each Website Gets Its Own Ad Code</div>
        <div style="font-size:13px;color:#1e3a8a;line-height:1.7;">
            Add all your websites below. After admin approves each one, you'll get a <strong>dedicated ad code locked to that domain</strong>.
            Clicks on an ad code are only counted when the visitor comes from the matching website — no leakage between sites.
        </div>
    </div>
</div>

<!-- Website list -->
@forelse($websites as $website)
<div class="card mb-4">
    <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
        <div style="flex:1;min-width:200px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                <span style="font-size:16px;font-weight:700;">{{ $website->domain }}</span>
                @if($website->isPending())
                    <span class="badge badge-warning">Pending Review</span>
                @elseif($website->isApproved())
                    <span class="badge badge-success">Approved</span>
                @else
                    <span class="badge badge-danger">Rejected</span>
                @endif
            </div>
            <div style="font-size:12px;color:#6b7280;margin-bottom:4px;">
                <a href="{{ $website->website_url }}" target="_blank" style="color:#01BF63;">{{ $website->website_url }}</a>
            </div>
            <div style="font-size:11px;color:#9ca3af;">Added {{ $website->created_at->diffForHumans() }}</div>

            @if($website->isRejected() && $website->rejection_reason)
            <div style="margin-top:10px;padding:10px 14px;background:#fff1f2;border:1px solid #fecaca;border-radius:8px;font-size:13px;color:#991b1b;">
                <strong>Rejection reason:</strong> {{ $website->rejection_reason }}
            </div>
            @endif
        </div>

        @if($website->stat_screenshots && count($website->stat_screenshots) > 0)
        <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
            @foreach($website->stat_screenshots as $i => $path)
            <a href="{{ asset('storage/' . $path) }}" target="_blank"
               style="display:block;border-radius:6px;overflow:hidden;border:1.5px solid #e5e7eb;transition:border-color 0.15s;"
               onmouseover="this.style.borderColor='#01BF63'" onmouseout="this.style.borderColor='#e5e7eb'">
                <img src="{{ asset('storage/' . $path) }}" alt="Screenshot {{ $i+1 }}"
                     style="width:80px;height:55px;object-fit:cover;display:block;">
            </a>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Ad Code section for approved websites -->
    @if($website->isApproved() && $website->trackingLink)
    @php $link = $website->trackingLink; @endphp
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f3f4f6;">
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <div style="background:#e6faf2;padding:5px 12px;border-radius:12px;font-size:12px;color:#065f46;font-weight:600;">
                ✓ {{ number_format($link->unique_clicks) }} valid clicks
            </div>
            <div style="font-size:12px;color:#9ca3af;">
                Tracking code: <span style="font-family:monospace;color:#374151;font-weight:600;">{{ $link->unique_code }}</span>
                — only counts clicks from <strong>{{ $website->domain }}</strong>
            </div>
        </div>
        <div style="background:#eff6ff;border:1.5px solid #93c5fd;border-radius:10px;padding:14px 18px;display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <div style="font-size:13px;font-weight:700;color:#1e40af;margin-bottom:4px;">Get your embed code from the Ad Code section</div>
                <div style="font-size:12px;color:#1e3a8a;line-height:1.6;">
                    Go to <strong>Ad Code</strong> in the sidebar → select tracking link <strong style="font-family:monospace;">{{ $link->name ?: $link->unique_code }}</strong> → choose a button style → copy the generated code.
                </div>
            </div>
            <a href="{{ route('publisher.adcode') }}" class="btn btn-primary btn-sm" style="flex-shrink:0;">Go to Ad Code →</a>
        </div>
    </div>
    @endif
</div>
@empty
<div class="card" style="text-align:center;padding:40px;">
    <div style="font-size:48px;margin-bottom:12px;">🌐</div>
    <div style="font-size:16px;font-weight:700;color:#374151;margin-bottom:6px;">No websites added yet</div>
    <div style="font-size:13px;color:#9ca3af;">Add your website below to get a dedicated ad code</div>
</div>
@endforelse

<!-- Add new website form -->
<div class="card mt-4">
    <div class="card-title mb-1">Add a New Website</div>
    <div class="card-subtitle mb-6" style="font-size:13px;">Submit your website for review. Upload screenshot(s) of your traffic statistics (Google Analytics, Yandex Metrica, etc.) as proof.</div>

    <form method="POST" action="{{ route('publisher.websites.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-4">
            <label class="form-label">Website URL *</label>
            <input type="url" name="website_url" class="form-control" value="{{ old('website_url') }}"
                   placeholder="https://yourwebsite.com" required>
            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Must be the exact domain where you'll place the ad code</div>
        </div>

        <!-- Screenshot upload -->
        <div class="form-group mb-4">
            <label class="form-label">Statistics Screenshots * <span style="font-size:11px;color:#9ca3af;font-weight:400;">(1–4 files, JPG/PNG/WebP, max 5MB each)</span></label>

            <div id="dropzone-new" onclick="document.getElementById('screenshots-new').click()"
                 style="border:2px dashed #d1d5db;border-radius:12px;padding:28px;text-align:center;cursor:pointer;background:#fafafa;transition:all 0.2s;"
                 ondragover="event.preventDefault();this.style.borderColor='#01BF63';this.style.background='#f0fdf4';"
                 ondragleave="this.style.borderColor='#d1d5db';this.style.background='#fafafa';"
                 ondrop="handleDropNew(event)">
                <svg width="32" height="32" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="1.5" style="margin-bottom:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <div style="font-size:14px;font-weight:600;color:#374151;">Click or drag screenshots here</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Accepted: Google Analytics, Yandex Metrica, GSC, Bing Webmaster</div>
            </div>
            <input type="file" id="screenshots-new" name="screenshots[]" accept="image/*" multiple style="display:none;" onchange="previewNew(this)">

            <div id="preview-new" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px;"></div>
        </div>

        <button type="submit" class="btn btn-primary">Submit Website for Review</button>
    </form>
</div>

<script>
function previewNew(input) {
    const preview = document.getElementById('preview-new');
    const existing = preview.querySelectorAll('.thumb-item').length;
    const files = Array.from(input.files);
    const allowed = 4 - existing;
    files.slice(0, allowed).forEach((file, i) => addThumbNew(file, existing + i));
    syncFilesNew();
}

let newFiles = [];

function addThumbNew(file, idx) {
    const preview = document.getElementById('preview-new');
    newFiles.push(file);
    const reader = new FileReader();
    const wrapper = document.createElement('div');
    wrapper.className = 'thumb-item';
    wrapper.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1.5px solid #e5e7eb;';
    wrapper.dataset.idx = newFiles.length - 1;
    reader.onload = e => {
        wrapper.innerHTML = `
            <img src="${e.target.result}" style="width:100px;height:70px;object-fit:cover;display:block;">
            <button type="button" onclick="removeThumbNew(this.parentElement)" style="position:absolute;top:3px;right:3px;background:rgba(0,0,0,0.6);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer;line-height:1;">×</button>`;
    };
    reader.readAsDataURL(file);
    preview.appendChild(wrapper);
}

function removeThumbNew(el) {
    const idx = parseInt(el.dataset.idx);
    newFiles.splice(idx, 1);
    el.remove();
    document.querySelectorAll('#preview-new .thumb-item').forEach((el, i) => el.dataset.idx = i);
    syncFilesNew();
}

function syncFilesNew() {
    const dt = new DataTransfer();
    newFiles.forEach(f => dt.items.add(f));
    document.getElementById('screenshots-new').files = dt.files;
}

function handleDropNew(event) {
    event.preventDefault();
    const dz = document.getElementById('dropzone-new');
    dz.style.borderColor = '#d1d5db';
    dz.style.background = '#fafafa';
    const files = Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    const existing = document.querySelectorAll('#preview-new .thumb-item').length;
    const allowed = 4 - existing;
    files.slice(0, allowed).forEach(f => addThumbNew(f));
    syncFilesNew();
}
</script>
@endsection
