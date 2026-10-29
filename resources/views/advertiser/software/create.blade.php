@extends('layouts.advertiser')
@section('title', 'New Campaign')
@section('page-title', 'Create Campaign')

@section('content')
@if($errors->any())
<div class="alert alert-danger mb-6">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
@endif

<div style="max-width:680px;">
    <div style="background:#eff6ff;border:1.5px solid #93c5fd;border-radius:14px;padding:18px 20px;margin-bottom:28px;">
        <div style="font-size:14px;font-weight:700;color:#1e40af;margin-bottom:8px;"><i class="fa-solid fa-circle-info"></i> How It Works</div>
        <div style="font-size:13px;color:#1e3a8a;line-height:1.8;">
            1. Enter your campaign name, upload your Windows <strong>.exe</strong> and (optionally) your <strong>.ico</strong> icon.<br>
            2. Choose how many installs you want and your offered rate per install.<br>
            3. Submit — our team reviews it. We may propose a rate; you approve it.<br>
            4. Once approved, we make it live and deliver <strong>exactly</strong> the number of installs you set — no more.<br>
            5. Track installs (region, device, IP) in your dashboard, and increase the target anytime.
        </div>
    </div>

    <div class="card">
        <div class="card-title mb-1"><i class="fa-solid fa-circle-plus"></i> Campaign Details</div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:24px;">Upload your software and set your install target. Our team will review and confirm pricing.</div>

        <form method="POST" action="{{ route('advertiser.campaigns.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Campaign Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. FontManager Pro — Windows">
            </div>

            <div class="form-group">
                <label class="form-label">Software Installer (.exe) *</label>
                <input type="file" id="installerFile" accept=".exe" class="form-control">
                <input type="hidden" name="installer_token" id="installerToken">
                <input type="hidden" name="installer_name" id="installerName">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Your real Windows setup file. Max 200 MB. Uploading starts as soon as you pick the file — wait for it to finish, then submit.</div>

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

            @include('advertiser.software._geo_fields', [
                'selectedMode'     => old('geo_mode', 'mixed'),
                'selectedExcluded' => old('excluded_countries', []),
            ])

            <div class="form-group">
                <label class="form-label">Additional Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Anything our team should know about this software">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit Campaign</button>
            <a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost" style="margin-left:8px;">Cancel</a>
            <div id="submitHint" style="font-size:12px;color:#9ca3af;margin-top:8px;">Upload your installer above first — the button enables once it finishes.</div>
        </form>
    </div>
</div>

<script>
(function(){
    var fileInput = document.getElementById('installerFile');
    var tokenInput = document.getElementById('installerToken');
    var nameInput = document.getElementById('installerName');
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
        submitBtn.style.opacity = on ? '1' : '.5';
        submitBtn.style.cursor = on ? '' : 'not-allowed';
        submitHint.style.display = on ? 'none' : 'block';
    }
    function fmt(b){ return b >= 1048576 ? (b/1048576).toFixed(1)+' MB' : Math.max(1,Math.round(b/1024))+' KB'; }
    setSubmit(false);

    fileInput.addEventListener('change', function(){
        var f = fileInput.files[0];
        if(!f) return;
        box.style.display = 'block';
        if(!/\.exe$/i.test(f.name)){
            statusEl.textContent = 'Please choose a .exe file.'; statusEl.style.color = '#dc2626';
            tokenInput.value = ''; setSubmit(false); return;
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
                    setSubmit(true);
                } catch(e){ statusEl.textContent = 'Upload failed, please try again.'; statusEl.style.color = '#dc2626'; }
            } else {
                var msg = 'Upload failed, please try again.';
                try{ msg = JSON.parse(xhr.responseText).message || msg; }catch(e){}
                statusEl.textContent = msg; statusEl.style.color = '#dc2626';
                tokenInput.value = ''; setSubmit(false);
            }
        };
        xhr.onerror = function(){ statusEl.textContent = 'Network error during upload.'; statusEl.style.color = '#dc2626'; };
        xhr.send(fd);
    });

    // Guard: don't allow submit without a finished upload.
    document.querySelector('form[action="{{ route('advertiser.campaigns.store') }}"]').addEventListener('submit', function(e){
        if(!tokenInput.value){ e.preventDefault(); setSubmit(false); if(box.style.display==='none'){ box.style.display='block'; statusEl.textContent='Please upload your installer first.'; statusEl.style.color='#dc2626'; } }
    });
})();
</script>
<style>@media(max-width:620px){.sw-grid{grid-template-columns:1fr !important;}}</style>
@endsection
