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
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="sw-grid">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Number of Installs *</label>
                            <input type="number" name="max_installs" class="form-control" value="{{ old('max_installs') }}" min="1" required placeholder="e.g. 100">
                            <div class="form-hint">We deliver exactly this many, then stop. Increase anytime.</div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Offered Rate (per install) *</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;">$</span>
                                <input type="number" name="offered_rate" class="form-control" value="{{ old('offered_rate') }}" min="0" step="0.01" required placeholder="0.40" style="padding-left:26px;">
                            </div>
                            <div class="form-hint">What you're offering per install.</div>
                        </div>
                    </div>
                    <div id="valueHint" style="display:none;margin-top:12px;font-size:13px;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;padding:10px 12px;">
                        <i class="fa-solid fa-calculator"></i> Estimated campaign value: <strong id="valueAmount">$0.00</strong>
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
            <div class="step"><div class="step-num">2</div><div class="step-txt">Choose how many installs you want and your offered rate per install.</div></div>
            <div class="step"><div class="step-num">3</div><div class="step-txt">We review it and may propose a rate — you approve it.</div></div>
            <div class="step"><div class="step-num">4</div><div class="step-txt">We make it live and deliver <strong>exactly</strong> the number you set — no more.</div></div>
            <div class="step" style="margin-bottom:0;"><div class="step-num">5</div><div class="step-txt">Track installs by region, device &amp; IP, and increase the target anytime.</div></div>

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

    // Live campaign value estimate.
    var maxEl = document.querySelector('[name="max_installs"]');
    var rateEl = document.querySelector('[name="offered_rate"]');
    var vHint = document.getElementById('valueHint');
    var vAmount = document.getElementById('valueAmount');
    function calcValue(){
        var n = parseFloat(maxEl.value), r = parseFloat(rateEl.value);
        if(n > 0 && r >= 0){ vAmount.textContent = '$' + (n*r).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}); vHint.style.display='block'; }
        else vHint.style.display='none';
    }
    maxEl.addEventListener('input', calcValue); rateEl.addEventListener('input', calcValue); calcValue();

    // Guard: don't submit without a finished upload.
    document.getElementById('campaignForm').addEventListener('submit', function(e){
        if(!tokenInput.value){ e.preventDefault(); setSubmit(false); box.style.display='block'; statusEl.textContent='Please upload your installer first.'; statusEl.style.color='#dc2626'; drop.scrollIntoView({behavior:'smooth',block:'center'}); }
    });
})();
</script>
@endsection
