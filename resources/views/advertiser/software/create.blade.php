@extends('layouts.advertiser')
@section('title', 'New Campaign')
@section('page-title', 'Create Campaign')

@section('content')
@if($errors->any())
<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><span>@foreach($errors->all() as $e){{ $e }}<br>@endforeach</span></div>
@endif

<form method="POST" action="{{ route('advertiser.campaigns.store') }}" enctype="multipart/form-data" id="campaignForm">
    @csrf
    <div class="create-grid">
        {{-- Main form --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:22px 24px;border-bottom:1px solid #f1f3f5;">
                <div class="card-title mb-1"><i class="fa-solid fa-circle-plus"></i> Campaign Details</div>
                <div style="font-size:13px;color:#9ca3af;">Upload your software and set your install target. Our team reviews and confirms pricing.</div>
            </div>

            <div style="padding:24px;">
                {{-- Section: Software --}}
                <div class="form-section">
                    <div class="section-head"><i class="fa-solid fa-box-open"></i> Software</div>

                    <div class="form-group">
                        <label class="form-label">Campaign Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. FontManager Pro — Windows">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Software Installer (.exe) *</label>
                        <label for="installerFile" class="dropzone" id="installerDrop">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <div class="dz-main" id="installerDzMain">Click to choose your installer (.exe)</div>
                            <div class="dz-sub">Windows setup file · up to 200 MB · uploads instantly</div>
                        </label>
                        <input type="file" id="installerFile" accept=".exe" style="display:none;">
                        <input type="hidden" name="installer_token" id="installerToken">
                        <input type="hidden" name="installer_name" id="installerName">

                        <div id="uploadBox" style="display:none;margin-top:12px;border:1px solid #e5e7eb;border-radius:10px;padding:12px 14px;background:#fafafa;">
                            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:8px;">
                                <span id="uploadFile" style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></span>
                                <span id="uploadPct" style="font-size:13px;font-weight:700;color:#3b82f6;flex-shrink:0;">0%</span>
                            </div>
                            <div style="background:#e5e7eb;border-radius:6px;height:8px;overflow:hidden;">
                                <div id="uploadBar" style="height:100%;width:0;background:linear-gradient(90deg,#3b82f6,#01BF63);transition:width .2s;"></div>
                            </div>
                            <div id="uploadStatus" style="font-size:12px;color:#6b7280;margin-top:8px;"></div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">App Icon (.ico, optional)</label>
                        <label for="iconFile" class="dropzone" id="iconDrop" style="padding:18px 16px;">
                            <i class="fa-regular fa-image"></i>
                            <div class="dz-main" id="iconDzMain">Choose your .ico icon</div>
                            <div class="dz-sub">Used as the download's icon so it shows your brand</div>
                        </label>
                        <input type="file" name="icon" id="iconFile" accept=".ico" style="display:none;">
                    </div>
                </div>

                {{-- Section: Pricing & target --}}
                <div class="form-section">
                    <div class="section-head"><i class="fa-solid fa-sack-dollar"></i> Pricing &amp; Target</div>

                    <div class="form-group">
                        <label class="form-label">Number of Installs *</label>
                        <input type="number" name="max_installs" class="form-control" value="{{ old('max_installs') }}" min="1" required placeholder="e.g. 100">
                        <div class="form-hint">We deliver exactly this many, then stop. Increase anytime.</div>
                    </div>

                    <label class="form-label">Offered Rates per install *</label>
                    <div class="form-hint" style="margin:-2px 0 10px;">Offer a rate for each tier. Higher tiers earn higher rates — each must be more than the one above.</div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;">
                        @php
                            $rateFields = [
                                ['offered_rate_mixed', 'Mixed (worldwide)', '🌍', '0.20'],
                                ['offered_rate_tier23', 'Tier 2 & 3', '🌐', '0.35'],
                                ['offered_rate_tier1', 'Tier 1', '⭐', '0.60'],
                            ];
                        @endphp
                        @foreach($rateFields as [$fname, $flabel, $ficon, $fph])
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:12px;background:#fafbfc;">
                            <div style="font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;">{{ $ficon }} {{ $flabel }}</div>
                            <div style="position:relative;">
                                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;">$</span>
                                <input type="number" name="{{ $fname }}" class="form-control rate-input" value="{{ old($fname) }}" min="0" step="0.01" required placeholder="{{ $fph }}" style="padding-left:24px;">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div id="rateWarn" style="display:none;margin-top:10px;font-size:12.5px;color:#b91c1c;"><i class="fa-solid fa-triangle-exclamation"></i> Each tier's rate must be higher than the tier above it (Tier 1 &gt; Tier 2 &amp; 3 &gt; Mixed).</div>
                    <div id="valueHint" style="display:none;margin-top:10px;font-size:13px;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:10px 12px;">
                        <i class="fa-solid fa-calculator"></i> Estimated value for your selected tier: <strong id="valueAmount">$0.00</strong>
                    </div>
                </div>

                {{-- Section: Targeting --}}
                <div class="form-section">
                    <div class="section-head"><i class="fa-solid fa-earth-americas"></i> Geo Targeting</div>
                    @include('advertiser.software._geo_fields', [
                        'selectedMode'     => old('geo_mode', 'mixed'),
                        'selectedExcluded' => old('excluded_countries', []),
                    ])
                </div>

                {{-- Section: Notes --}}
                <div class="form-section">
                    <div class="section-head"><i class="fa-regular fa-note-sticky"></i> Notes</div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Additional Notes (optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Anything our team should know about this software">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div style="padding:18px 24px;border-top:1px solid #f1f3f5;background:#fafbfc;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <button type="submit" id="submitBtn" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit Campaign</button>
                <a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost">Cancel</a>
                <span id="submitHint" style="font-size:12px;color:#9ca3af;"><i class="fa-regular fa-circle-up"></i> Upload your installer first — the button enables once it finishes.</span>
            </div>
        </div>

        {{-- Guide --}}
        <div class="card guide-card">
            <div class="card-title" style="margin-bottom:16px;"><i class="fa-solid fa-circle-info"></i> How It Works</div>
            <div class="step"><div class="step-num">1</div><div class="step-txt">Name your campaign and upload your Windows <strong>.exe</strong> (and optionally your <strong>.ico</strong> icon).</div></div>
            <div class="step"><div class="step-num">2</div><div class="step-txt">Choose how many installs you want and your offered rate <strong>per tier</strong> (Mixed, Tier 2&amp;3, Tier 1) — each tier higher than the last.</div></div>
            <div class="step"><div class="step-num">3</div><div class="step-txt">We review it and may propose a rate — you approve it.</div></div>
            <div class="step"><div class="step-num">4</div><div class="step-txt">We make it live and deliver <strong>exactly</strong> the number you set — and you can <strong>increase the number of installs anytime</strong>.</div></div>
            <div class="step" style="margin-bottom:0;"><div class="step-num">5</div><div class="step-txt">Track installs by region, device &amp; IP in real time from your dashboard.</div></div>

            <div style="margin-top:18px;border-top:1px solid #f1f3f5;padding-top:16px;display:flex;gap:10px;align-items:flex-start;">
                <i class="fa-solid fa-shield-halved" style="color:var(--primary);margin-top:2px;"></i>
                <div style="font-size:12px;color:#6b7280;line-height:1.5;">Your installer is stored securely and delivered to users <strong>exactly as uploaded</strong> — we never modify it.</div>
            </div>
        </div>
    </div>
</form>

<style>
    .create-grid { display:grid; grid-template-columns: minmax(0,1fr) 300px; gap:22px; align-items:start; }
    .form-section { border-top:1px solid #f1f3f5; padding-top:22px; margin-top:22px; }
    .form-section:first-child { border-top:none; padding-top:0; margin-top:0; }
    .section-head { font-size:13px; font-weight:700; color:#111827; margin-bottom:16px; display:flex; align-items:center; gap:8px; text-transform:uppercase; letter-spacing:.04em; }
    .section-head i { color: var(--accent); font-size:14px; }
    .dropzone { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:5px; text-align:center; border:2px dashed #cbd5e1; border-radius:12px; padding:26px 16px; cursor:pointer; background:#fafbfc; transition:all .15s; }
    .dropzone:hover { border-color: var(--accent); background:#f5f9ff; }
    .dropzone i { font-size:24px; color: var(--accent); }
    .dropzone .dz-main { font-size:14px; font-weight:600; color:#334155; }
    .dropzone .dz-sub { font-size:12px; color:#94a3b8; }
    .dropzone.has-file { border-style:solid; border-color:#6ee7b7; background:#f0fdf4; }
    .dropzone.has-file i { color:#059669; }
    .guide-card .step { display:flex; gap:12px; margin-bottom:14px; }
    .guide-card .step-num { width:26px;height:26px;border-radius:50%;background:var(--accent-light);color:var(--accent-dark);font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    @media (max-width:860px){ .create-grid{ grid-template-columns:1fr; } .guide-card{ order:-1; } .sw-grid{ grid-template-columns:1fr !important; } }
</style>

<script>
(function(){
    var fileInput = document.getElementById('installerFile');
    var tokenInput = document.getElementById('installerToken');
    var nameInput = document.getElementById('installerName');
    var drop = document.getElementById('installerDrop');
    var dzMain = document.getElementById('installerDzMain');
    var box = document.getElementById('uploadBox');
    var bar = document.getElementById('uploadBar');
    var pct = document.getElementById('uploadPct');
    var statusEl = document.getElementById('uploadStatus');
    var fileLabel = document.getElementById('uploadFile');
    var submitBtn = document.getElementById('submitBtn');
    var submitHint = document.getElementById('submitHint');
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var url = "{{ route('advertiser.campaigns.upload-installer') }}";

    function setSubmit(on){
        submitBtn.disabled = !on;
        submitHint.style.display = on ? 'none' : 'inline';
    }
    function fmt(b){ return b >= 1048576 ? (b/1048576).toFixed(1)+' MB' : Math.max(1,Math.round(b/1024))+' KB'; }
    setSubmit(false);

    fileInput.addEventListener('change', function(){
        var f = fileInput.files[0];
        if(!f) return;
        box.style.display = 'block';
        if(!/\.exe$/i.test(f.name)){
            statusEl.textContent = 'Please choose a .exe file.'; statusEl.style.color = '#dc2626';
            tokenInput.value = ''; setSubmit(false); drop.classList.remove('has-file'); return;
        }
        tokenInput.value = ''; setSubmit(false);
        fileLabel.textContent = f.name + ' · ' + fmt(f.size);
        bar.style.width = '0%'; pct.textContent = '0%';
        statusEl.textContent = 'Uploading…'; statusEl.style.color = '#6b7280';

        var fd = new FormData();
        fd.append('installer', f);
        fd.append('_token', csrf);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.upload.onprogress = function(e){
            if(e.lengthComputable){
                var p = Math.round(e.loaded / e.total * 100);
                bar.style.width = p + '%'; pct.textContent = p + '%';
                if(p >= 100) statusEl.textContent = 'Finishing…';
            }
        };
        xhr.onload = function(){
            if(xhr.status === 200){
                try{
                    var r = JSON.parse(xhr.responseText);
                    tokenInput.value = r.token; nameInput.value = r.name;
                    bar.style.width = '100%'; pct.textContent = '100%';
                    statusEl.textContent = '✓ Uploaded — ready to submit'; statusEl.style.color = '#059669';
                    drop.classList.add('has-file'); dzMain.textContent = r.name + ' · uploaded ✓';
                    setSubmit(true);
                } catch(e){ statusEl.textContent = 'Upload failed, please try again.'; statusEl.style.color = '#dc2626'; }
            } else {
                var msg = 'Upload failed, please try again.';
                try{ msg = JSON.parse(xhr.responseText).message || msg; }catch(e){}
                statusEl.textContent = msg; statusEl.style.color = '#dc2626';
                tokenInput.value = ''; setSubmit(false); drop.classList.remove('has-file');
            }
        };
        xhr.onerror = function(){ statusEl.textContent = 'Network error during upload.'; statusEl.style.color = '#dc2626'; };
        xhr.send(fd);
    });

    // Icon dropzone: show chosen filename.
    var iconFile = document.getElementById('iconFile');
    var iconDrop = document.getElementById('iconDrop');
    var iconMain = document.getElementById('iconDzMain');
    if(iconFile){
        iconFile.addEventListener('change', function(){
            if(iconFile.files[0]){ iconMain.textContent = iconFile.files[0].name; iconDrop.classList.add('has-file'); }
            else { iconMain.textContent = 'Choose your .ico icon'; iconDrop.classList.remove('has-file'); }
        });
    }

    // Tiered rates: value estimate (for selected tier) + ordering check.
    var maxEl = document.querySelector('[name="max_installs"]');
    var rateInputs = {
        mixed:  document.querySelector('[name="offered_rate_mixed"]'),
        tier23: document.querySelector('[name="offered_rate_tier23"]'),
        tier1:  document.querySelector('[name="offered_rate_tier1"]')
    };
    var vHint = document.getElementById('valueHint');
    var vAmount = document.getElementById('valueAmount');
    var rateWarn = document.getElementById('rateWarn');

    function activeMode(){ var r = document.querySelector('[name="geo_mode"]:checked'); return r ? r.value : 'mixed'; }
    function num(el){ var v = parseFloat(el.value); return isNaN(v) ? null : v; }
    function orderOk(){
        var m = num(rateInputs.mixed), t = num(rateInputs.tier23), o = num(rateInputs.tier1);
        if(m === null || t === null || o === null) return true; // incomplete — don't warn yet
        return t > m && o > t;
    }
    function refresh(){
        if(rateWarn) rateWarn.style.display = orderOk() ? 'none' : 'block';
        var r = num(rateInputs[activeMode()]), n = parseFloat(maxEl.value);
        if(n > 0 && r !== null){ vAmount.textContent = '$' + (n*r).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}); vHint.style.display='block'; }
        else vHint.style.display='none';
    }
    maxEl.addEventListener('input', refresh);
    Object.keys(rateInputs).forEach(function(k){ rateInputs[k].addEventListener('input', refresh); });
    document.querySelectorAll('[name="geo_mode"]').forEach(function(r){ r.addEventListener('change', refresh); });
    refresh();

    // Guard: finished upload + valid rate ladder before submit.
    document.getElementById('campaignForm').addEventListener('submit', function(e){
        if(!tokenInput.value){ e.preventDefault(); setSubmit(false); box.style.display='block'; statusEl.textContent='Please upload your installer first.'; statusEl.style.color='#dc2626'; drop.scrollIntoView({behavior:'smooth',block:'center'}); return; }
        if(!orderOk()){ e.preventDefault(); rateWarn.style.display='block'; rateWarn.scrollIntoView({behavior:'smooth',block:'center'}); }
    });
})();
</script>
@endsection
