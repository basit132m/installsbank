@extends('layouts.advertiser')
@section('title', $softwareCampaign->name)
@section('page-title', $softwareCampaign->name)

@section('content')

{{-- Header --}}
<div class="card" style="margin-bottom:20px;padding:18px 20px;display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:14px;min-width:0;">
        <div style="width:48px;height:48px;border-radius:13px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;"><i class="fa-solid fa-compact-disc"></i></div>
        <div style="min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="font-size:17px;font-weight:800;">{{ $softwareCampaign->name }}</span>
                <span class="badge {{ $softwareCampaign->statusBadgeClass() }}">{{ $softwareCampaign->statusLabel() }}</span>
            </div>
            <div style="font-size:12.5px;color:#6b7280;margin-top:3px;">
                <i class="fa-solid fa-tag"></i> ${{ number_format($softwareCampaign->effectiveRate(), 2) }} / install
                · <i class="fa-solid fa-bullseye"></i> target {{ number_format($softwareCampaign->max_installs) }}
                · <i class="fa-regular fa-file-lines"></i> {{ $softwareCampaign->payload_original_name }}
            </div>
        </div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <a href="{{ route('advertiser.campaigns.index') }}" class="btn btn-ghost btn-sm"><i class="fa-solid fa-arrow-left"></i> All</a>
        @if($softwareCampaign->enabled)
        <form method="POST" action="{{ route('advertiser.campaigns.stop', $softwareCampaign) }}" onsubmit="return confirm('Stop this campaign? Traffic will be redirected to the fallback link until you resume.');">
            @csrf
            <button class="btn btn-sm" style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;"><i class="fa-solid fa-circle-stop"></i> Stop</button>
        </form>
        @elseif($softwareCampaign->status === 'paused')
        <form method="POST" action="{{ route('advertiser.campaigns.start', $softwareCampaign) }}">
            @csrf
            <button class="btn btn-sm btn-primary"><i class="fa-solid fa-circle-play"></i> Resume</button>
        </form>
        @endif
    </div>
</div>

@if($softwareCampaign->status === 'paused' && !$softwareCampaign->enabled)
<div class="alert alert-warning"><i class="fa-solid fa-circle-pause"></i><span>This campaign is <strong>stopped</strong>. Visitors are redirected to the fallback link — no new installs are collected until you resume.</span></div>
@endif

{{-- Rate offer banner --}}
@if($softwareCampaign->status === 'rate_offered')
<div class="card" style="margin-bottom:20px;border:1.5px solid #fde68a;background:#fffbeb;">
    <div style="font-size:15px;font-weight:700;color:#92400e;margin-bottom:6px;"><i class="fa-solid fa-handshake-simple"></i> Rate proposal — your approval needed</div>
    <div style="font-size:13px;color:#92400e;margin-bottom:14px;">
        You offered <strong>${{ number_format($softwareCampaign->offered_rate, 2) }}</strong> / install.
        Our team proposed <strong>${{ number_format($softwareCampaign->admin_rate, 2) }}</strong> / install.
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <form method="POST" action="{{ route('advertiser.campaigns.accept-rate', $softwareCampaign) }}">
            @csrf
            <button class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i> Accept ${{ number_format($softwareCampaign->admin_rate, 2) }} / install</button>
        </form>
        <form method="POST" action="{{ route('advertiser.campaigns.reject-rate', $softwareCampaign) }}" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="text" name="reason" class="form-control" placeholder="Reason (optional)" style="width:200px;height:34px;">
            <button class="btn btn-ghost btn-sm" style="color:#dc2626;">Decline</button>
        </form>
    </div>
</div>
@endif

{{-- Stat row --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa-solid fa-download"></i></div>
        <div class="stat-value" style="color:#3b82f6;">{{ number_format($installsToday) }}</div>
        <div class="stat-label">Installs Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3f4f6;color:#374151;"><i class="fa-solid fa-chart-simple"></i></div>
        <div class="stat-value">{{ number_format($installsTotal) }}</div>
        <div class="stat-label">Total Installs</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e6faf2;color:#059669;"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-value" style="color:#059669;">{{ number_format(max(0, $softwareCampaign->max_installs - $installsTotal)) }}</div>
        <div class="stat-label">Remaining</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-tag"></i></div>
        <div class="stat-value">${{ number_format($softwareCampaign->effectiveRate(), 2) }}</div>
        <div class="stat-label">Rate / install</div>
    </div>
</div>

{{-- Target progress + increase --}}
<div class="card" style="margin-bottom:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:12px;">
        <div class="card-title"><i class="fa-solid fa-bullseye"></i> Install Target</div>
        <div style="font-size:13px;color:#6b7280;">{{ number_format($installsTotal) }} / {{ number_format($softwareCampaign->max_installs) }} ({{ $softwareCampaign->progressPercent() }}%)</div>
    </div>
    <div style="background:#f3f4f6;border-radius:6px;height:10px;overflow:hidden;margin-bottom:16px;">
        <div style="height:100%;width:{{ $softwareCampaign->progressPercent() }}%;background:linear-gradient(90deg,#3b82f6,#01BF63);border-radius:6px;"></div>
    </div>
    <form method="POST" action="{{ route('advertiser.campaigns.increase-installs', $softwareCampaign) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        @csrf
        <span style="font-size:13px;color:#6b7280;">Increase target to</span>
        <input type="number" name="max_installs" class="form-control" min="{{ $softwareCampaign->max_installs + 1 }}" value="{{ $softwareCampaign->max_installs + 100 }}" style="width:140px;">
        <button class="btn btn-ghost btn-sm"><i class="fa-solid fa-arrow-up"></i> Update target</button>
    </form>
</div>

{{-- Software file --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:6px;"><i class="fa-regular fa-file-code"></i> Software File</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">
        Current installer: <strong>{{ $softwareCampaign->payload_original_name }}</strong>
        <span style="color:#9ca3af;">({{ number_format(($softwareCampaign->payload_size ?: 0) / 1048576, 2) }} MB)</span>.
        Upload a new <code>.exe</code> to replace it — new downloads use the new file immediately.
    </p>
    <form method="POST" action="{{ route('advertiser.campaigns.replace-installer', $softwareCampaign) }}" id="replaceForm">
        @csrf
        <label for="rInstaller" style="display:flex;align-items:center;gap:12px;border:2px dashed #cbd5e1;border-radius:12px;padding:16px;cursor:pointer;background:#fafbfc;" id="rDrop">
            <i class="fa-solid fa-cloud-arrow-up" style="font-size:22px;color:var(--accent);"></i>
            <span>
                <span id="rMain" style="font-size:14px;font-weight:600;color:#334155;">Click to choose a new installer (.exe)</span>
                <span style="display:block;font-size:12px;color:#94a3b8;">Max 200 MB · uploads instantly</span>
            </span>
        </label>
        <input type="file" id="rInstaller" accept=".exe" style="display:none;">
        <input type="hidden" name="installer_token" id="rToken">
        <input type="hidden" name="installer_name" id="rName">

        <div id="rBox" style="display:none;margin-top:12px;border:1px solid #e5e7eb;border-radius:10px;padding:12px 14px;background:#fafafa;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:8px;">
                <span id="rFile" style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></span>
                <span id="rPct" style="font-size:13px;font-weight:700;color:#3b82f6;flex-shrink:0;">0%</span>
            </div>
            <div style="background:#e5e7eb;border-radius:6px;height:8px;overflow:hidden;"><div id="rBar" style="height:100%;width:0;background:linear-gradient(90deg,#3b82f6,#01BF63);transition:width .2s;"></div></div>
            <div id="rStatus" style="font-size:12px;color:#6b7280;margin-top:8px;"></div>
        </div>

        <button type="submit" id="rSubmit" class="btn btn-primary btn-sm" style="margin-top:14px;" disabled><i class="fa-solid fa-arrows-rotate"></i> Replace file</button>
    </form>
</div>

<script>
(function(){
    var f=document.getElementById('rInstaller'), tok=document.getElementById('rToken'), nm=document.getElementById('rName');
    var drop=document.getElementById('rDrop'), main=document.getElementById('rMain');
    var box=document.getElementById('rBox'), bar=document.getElementById('rBar'), pct=document.getElementById('rPct'), st=document.getElementById('rStatus'), fl=document.getElementById('rFile');
    var sub=document.getElementById('rSubmit');
    var csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var url="{{ route('advertiser.campaigns.upload-installer') }}";
    function fmt(b){ return b>=1048576?(b/1048576).toFixed(1)+' MB':Math.max(1,Math.round(b/1024))+' KB'; }
    function en(on){ sub.disabled=!on; }
    f.addEventListener('change', function(){
        var file=f.files[0]; if(!file) return;
        box.style.display='block';
        if(!/\.exe$/i.test(file.name)){ st.textContent='Please choose a .exe file.'; st.style.color='#dc2626'; tok.value=''; en(false); return; }
        tok.value=''; en(false); fl.textContent=file.name+' · '+fmt(file.size); bar.style.width='0%'; pct.textContent='0%'; st.textContent='Uploading…'; st.style.color='#6b7280';
        var fd=new FormData(); fd.append('installer',file); fd.append('_token',csrf);
        var x=new XMLHttpRequest(); x.open('POST',url,true); x.setRequestHeader('X-CSRF-TOKEN',csrf); x.setRequestHeader('Accept','application/json');
        x.upload.onprogress=function(e){ if(e.lengthComputable){ var p=Math.round(e.loaded/e.total*100); bar.style.width=p+'%'; pct.textContent=p+'%'; if(p>=100) st.textContent='Finishing…'; } };
        x.onload=function(){ if(x.status===200){ try{ var r=JSON.parse(x.responseText); tok.value=r.token; nm.value=r.name; bar.style.width='100%'; pct.textContent='100%'; st.textContent='✓ Uploaded — click "Replace file" to save'; st.style.color='#059669'; main.textContent=r.name+' · ready'; drop.style.borderColor='#6ee7b7'; drop.style.background='#f0fdf4'; en(true); }catch(e){ st.textContent='Upload failed, try again.'; st.style.color='#dc2626'; } } else { var m='Upload failed, try again.'; try{ m=JSON.parse(x.responseText).message||m; }catch(e){} st.textContent=m; st.style.color='#dc2626'; tok.value=''; en(false); } };
        x.onerror=function(){ st.textContent='Network error during upload.'; st.style.color='#dc2626'; };
        x.send(fd);
    });
    document.getElementById('replaceForm').addEventListener('submit', function(e){ if(!tok.value){ e.preventDefault(); box.style.display='block'; st.textContent='Upload a new installer first.'; st.style.color='#dc2626'; } });
})();
</script>

{{-- Billing --}}
@php
    $rate = $softwareCampaign->effectiveRate();
    $totalValue = $softwareCampaign->totalValue();
    $used = $softwareCampaign->valueDelivered();
    $balance = round($amountPaid - $used, 2);
    $prepaidInstalls = $softwareCampaign->installsCovered($amountPaid);
    $prepaidRemaining = $prepaidInstalls - $softwareCampaign->total_installs;
    $outstanding = max(0, round($totalValue - $amountPaid, 2));
@endphp
<div class="card" style="margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:6px;"><i class="fa-solid fa-wallet"></i> Billing</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">
        You pay <strong>${{ number_format($rate, 2) }}</strong> per install. Payments are made directly to our team
        (outside this panel) — your balance here updates automatically as installs are delivered.
    </p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:18px;">
        <div style="background:#f9fafb;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Campaign value</div>
            <div style="font-size:22px;font-weight:800;">${{ number_format($totalValue, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ number_format($softwareCampaign->max_installs) }} × ${{ number_format($rate, 2) }}</div>
        </div>
        <div style="background:#eff6ff;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Advance paid</div>
            <div style="font-size:22px;font-weight:800;color:#3b82f6;">${{ number_format($amountPaid, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">covers {{ number_format($prepaidInstalls) }} installs</div>
        </div>
        <div style="background:#f9fafb;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Delivered (used)</div>
            <div style="font-size:22px;font-weight:800;">−${{ number_format($used, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ number_format($softwareCampaign->total_installs) }} installs</div>
        </div>
        <div style="background:{{ $balance >= 0 ? '#d1fae5' : '#fee2e2' }};border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Balance</div>
            <div style="font-size:22px;font-weight:800;color:{{ $balance >= 0 ? '#065f46' : '#b91c1c' }};">${{ number_format($balance, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ $balance >= 0 ? number_format(max(0,$prepaidRemaining)).' installs left (prepaid)' : 'amount due' }}</div>
        </div>
    </div>

    @if($outstanding > 0)
    <div style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:8px;padding:12px 14px;font-size:13px;margin-bottom:8px;">
        To fully fund this campaign ({{ number_format($softwareCampaign->max_installs) }} installs), <strong>${{ number_format($outstanding, 2) }}</strong> is still to be paid. Send it to our team and we'll record it here.
    </div>
    @endif

    @if($payments->isNotEmpty())
    <div style="font-size:13px;font-weight:700;margin:14px 0 8px;"><i class="fa-solid fa-receipt"></i> Payments received</div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Date</th><th>Amount</th><th>Note</th></tr></thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td style="font-size:12px;">{{ $p->created_at->format('M d, Y') }}</td>
                    <td style="font-size:13px;font-weight:600;color:#059669;">+${{ number_format($p->amount, 2) }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->note ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Geo targeting --}}
<div class="card" style="margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:6px;"><i class="fa-solid fa-earth-americas"></i> Geo Targeting</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">Control which countries your installs come from. Changes apply to your links immediately.</p>
    <form method="POST" action="{{ route('advertiser.campaigns.geo', $softwareCampaign) }}">
        @csrf
        @include('advertiser.software._geo_fields', [
            'selectedMode'     => $softwareCampaign->geo_mode ?? 'mixed',
            'selectedExcluded' => $softwareCampaign->excluded_countries ?? [],
        ])
        <button class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk"></i> Save geo targeting</button>
        <span style="font-size:12px;color:#6b7280;margin-left:10px;">Currently: <strong>{{ $softwareCampaign->geoModeLabel() }}</strong>@if(!empty($softwareCampaign->excluded_countries)) · excluding {{ implode(', ', $softwareCampaign->excluded_countries) }}@endif</span>
    </form>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;" class="sw-grid">
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;"><i class="fa-solid fa-earth-americas"></i> Installs by Region</div>
        @forelse($countryBreakdown as $row)
        @php $fc = strtolower($row->country_code ?? ''); @endphp
        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
            <span style="display:flex;align-items:center;gap:8px;min-width:0;">
                @if($fc && $fc !== 'xx')
                <img src="https://flagcdn.com/24x18/{{ $fc }}.png" srcset="https://flagcdn.com/48x36/{{ $fc }}.png 2x" width="24" height="18" alt="" loading="lazy" style="border-radius:3px;box-shadow:0 0 0 1px rgba(0,0,0,.06);flex-shrink:0;">
                @else
                <span style="width:24px;text-align:center;flex-shrink:0;">🌐</span>
                @endif
                <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $row->country_name ?: 'Unknown' }}</span>
            </span>
            <strong style="flex-shrink:0;">{{ number_format($row->installs) }}</strong>
        </div>
        @empty
        <div style="color:#9ca3af;font-size:13px;">No installs yet</div>
        @endforelse
    </div>
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;"><i class="fa-solid fa-desktop"></i> Installs by Device</div>
        @forelse($deviceBreakdown as $row)
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f3f4f6;font-size:13px;">
            <span>{{ ucfirst($row->device_type ?: 'desktop') }}</span>
            <strong>{{ number_format($row->installs) }}</strong>
        </div>
        @empty
        <div style="color:#9ca3af;font-size:13px;">No installs yet</div>
        @endforelse
    </div>
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:16px;"><i class="fa-solid fa-list"></i> Install Log</div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>When</th><th>Region</th><th>IP Address</th><th>Device</th><th>OS</th></tr></thead>
            <tbody>
                @php $cc = $softwareCampaign->countColumn(); @endphp
                @forelse($installs as $i)
                <tr>
                    <td style="font-size:12px;">{{ $i->{$cc}?->format('M d, H:i') ?? '—' }}</td>
                    @php $lc = strtolower($i->country_code ?? ''); @endphp
                    <td style="font-size:13px;"><span style="display:inline-flex;align-items:center;gap:6px;">@if($lc && $lc !== 'xx')<img src="https://flagcdn.com/24x18/{{ $lc }}.png" width="20" height="15" alt="" loading="lazy" style="border-radius:2px;box-shadow:0 0 0 1px rgba(0,0,0,.06);">@endif{{ $i->country_name ?: ($i->country_code ?: '—') }}</span></td>
                    <td style="font-size:12px;font-family:monospace;">{{ $i->ip_address ?: '—' }}</td>
                    <td style="font-size:13px;">{{ ucfirst($i->device_type ?: 'desktop') }}</td>
                    <td style="font-size:12px;">{{ trim(($i->os ?: '') . ' ' . ($i->os_version ?: '')) ?: '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:32px;color:#9ca3af;">No installs recorded yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $installs->links() }}</div>
</div>

{{-- Danger zone --}}
<div class="card" style="margin-top:24px;border:1px solid #fecaca;">
    <div class="card-title" style="color:#b91c1c;margin-bottom:6px;"><i class="fa-solid fa-triangle-exclamation"></i> Danger Zone</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">Permanently delete this campaign along with its installs, links and billing records. This cannot be undone.</p>
    <form method="POST" action="{{ route('advertiser.campaigns.destroy', $softwareCampaign) }}"
          onsubmit="return confirm('Delete “{{ $softwareCampaign->name }}” permanently?\n\nThis removes the campaign and ALL its data (installs, links, payment records) and cannot be undone.');">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm" style="background:#dc2626;color:#fff;"><i class="fa-solid fa-trash"></i> Delete campaign</button>
    </form>
</div>

<style>@media(max-width:860px){.sw-grid{grid-template-columns:1fr !important;}}</style>
@endsection
