@extends('layouts.admin')
@section('title', $softwareCampaign->name)
@section('page-title', 'Software Campaign — ' . $softwareCampaign->name)

@section('content')

<a href="{{ route('admin.software-campaigns.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">&larr; All campaigns</a>

@if(!$stubReady)
<div class="alert" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;margin-bottom:20px;">
    No global stub binary is uploaded yet. The download link will not serve until you upload a stub on the
    <a href="{{ route('admin.software-campaigns.index') }}" style="text-decoration:underline;">campaigns list</a>.
</div>
@endif

{{-- Stat row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;margin-bottom:20px;">
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Installs Today</div><div style="font-size:26px;font-weight:800;color:#3b82f6;">{{ number_format($installsToday) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Installs / Target</div><div style="font-size:26px;font-weight:800;">{{ number_format($installsTotal) }}<span style="font-size:14px;color:#9ca3af;font-weight:600;">/{{ number_format($softwareCampaign->max_installs) }}</span></div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Remaining</div><div style="font-size:26px;font-weight:800;color:#059669;">{{ number_format(max(0, $softwareCampaign->max_installs - $installsTotal)) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Downloads</div><div style="font-size:26px;font-weight:800;color:#6b7280;">{{ number_format($downloadsTotal) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Status</div><div style="font-size:18px;font-weight:800;">{{ $softwareCampaign->statusLabel() }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Counting Mode</div><div style="font-size:18px;font-weight:800;">{{ $softwareCampaign->install_mode==='open'?'On open':'On install' }}</div></div>
</div>

{{-- Rate & approval workflow --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-title" style="margin-bottom:12px;">Rate &amp; Approval</div>
    <div style="display:flex;gap:28px;flex-wrap:wrap;margin-bottom:16px;font-size:13px;">
        <div><div style="color:#9ca3af;">Advertiser offered</div><div style="font-size:18px;font-weight:800;">${{ number_format($softwareCampaign->offered_rate, 2) }}</div></div>
        @if($softwareCampaign->admin_rate !== null)
        <div><div style="color:#9ca3af;">You proposed</div><div style="font-size:18px;font-weight:800;color:#d97706;">${{ number_format($softwareCampaign->admin_rate, 2) }}</div></div>
        @endif
        @if($softwareCampaign->final_rate !== null)
        <div><div style="color:#9ca3af;">Agreed rate</div><div style="font-size:18px;font-weight:800;color:#059669;">${{ number_format($softwareCampaign->final_rate, 2) }}</div></div>
        @endif
    </div>

    @if($softwareCampaign->status === 'pending_review')
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <form method="POST" action="{{ route('admin.software-campaigns.approve', $softwareCampaign) }}">
                @csrf
                <button class="btn btn-primary btn-sm">Approve at ${{ number_format($softwareCampaign->offered_rate, 2) }}</button>
            </form>
            <form method="POST" action="{{ route('admin.software-campaigns.counter-rate', $softwareCampaign) }}" style="display:flex;gap:8px;align-items:center;">
                @csrf
                <span style="color:#9ca3af;font-size:13px;">or propose $</span>
                <input type="number" name="admin_rate" step="0.01" min="0" class="form-control" style="width:110px;" value="{{ number_format($softwareCampaign->offered_rate, 2, '.', '') }}" required>
                <button class="btn btn-ghost btn-sm">Send counter</button>
            </form>
        </div>
    @elseif($softwareCampaign->status === 'rate_offered')
        <div style="font-size:13px;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;">⏳ Waiting for the advertiser to accept your ${{ number_format($softwareCampaign->admin_rate, 2) }} proposal.</div>
    @elseif($softwareCampaign->status === 'approved')
        <form method="POST" action="{{ route('admin.software-campaigns.make-live', $softwareCampaign) }}">
            @csrf
            <button class="btn btn-primary btn-sm">Make live</button>
            <span style="font-size:12px;color:#9ca3af;margin-left:8px;">Rate agreed at ${{ number_format($softwareCampaign->effectiveRate(), 2) }}. Make sure a stub is uploaded.</span>
        </form>
    @elseif($softwareCampaign->enabled)
        <form method="POST" action="{{ route('admin.software-campaigns.pause', $softwareCampaign) }}">
            @csrf
            <button class="btn btn-ghost btn-sm" style="color:#d97706;">Pause campaign</button>
        </form>
    @elseif($softwareCampaign->status === 'paused')
        <form method="POST" action="{{ route('admin.software-campaigns.resume', $softwareCampaign) }}">
            @csrf
            <button class="btn btn-primary btn-sm">Resume campaign</button>
        </form>
    @elseif($softwareCampaign->status === 'completed')
        <div style="font-size:13px;color:#374151;">✓ Install target reached. The advertiser can raise the target to continue.</div>
    @endif
</div>

{{-- Billing --}}
@php
    $rate = $softwareCampaign->effectiveRate();
    $totalValue = $softwareCampaign->totalValue();
    $used = $softwareCampaign->valueDelivered();
    $balance = round($amountPaid - $used, 2);
    $prepaidInstalls = $softwareCampaign->installsCovered($amountPaid);
    $prepaidRemaining = $prepaidInstalls - $softwareCampaign->total_installs;
@endphp
<div class="card" style="margin-bottom:20px;">
    <div class="card-title" style="margin-bottom:14px;">Billing — ${{ number_format($rate, 2) }} / install</div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:18px;">
        <div style="background:#f9fafb;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Campaign value</div>
            <div style="font-size:20px;font-weight:800;">${{ number_format($totalValue, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ number_format($softwareCampaign->max_installs) }} installs</div>
        </div>
        <div style="background:#eff6ff;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Advance paid</div>
            <div style="font-size:20px;font-weight:800;color:#3b82f6;">${{ number_format($amountPaid, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">covers {{ number_format($prepaidInstalls) }} installs</div>
        </div>
        <div style="background:#f9fafb;border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Delivered (used)</div>
            <div style="font-size:20px;font-weight:800;">−${{ number_format($used, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ number_format($softwareCampaign->total_installs) }} installs</div>
        </div>
        <div style="background:{{ $balance >= 0 ? '#d1fae5' : '#fee2e2' }};border-radius:10px;padding:14px;">
            <div style="font-size:12px;color:#9ca3af;">Balance</div>
            <div style="font-size:20px;font-weight:800;color:{{ $balance >= 0 ? '#065f46' : '#b91c1c' }};">${{ number_format($balance, 2) }}</div>
            <div style="font-size:11px;color:#9ca3af;">{{ $balance >= 0 ? number_format(max(0,$prepaidRemaining)).' prepaid left' : 'advertiser owes' }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.software-campaigns.payments.store', $softwareCampaign) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;border-top:1px solid #f3f4f6;padding-top:16px;">
        @csrf
        <span style="font-size:13px;color:#6b7280;">Record payment received: $</span>
        <input type="number" name="amount" step="0.01" min="0.01" class="form-control" style="width:140px;" placeholder="0.00" required>
        <input type="text" name="note" class="form-control" style="width:220px;" placeholder="Note (e.g. BTC, advance)">
        <button class="btn btn-primary btn-sm">Add payment</button>
    </form>

    @if($payments->isNotEmpty())
    <div style="overflow-x:auto;margin-top:16px;">
        <table>
            <thead><tr><th>Date</th><th>Amount</th><th>Note</th><th></th></tr></thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td style="font-size:12px;">{{ $p->created_at->format('M d, Y H:i') }}</td>
                    <td style="font-size:13px;font-weight:600;color:#059669;">+${{ number_format($p->amount, 2) }}</td>
                    <td style="font-size:12px;color:#6b7280;">{{ $p->note ?: '—' }}</td>
                    <td><form method="POST" action="{{ route('admin.software-campaigns.payments.delete', [$softwareCampaign, $p]) }}" onsubmit="return confirm('Remove this payment entry?');">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm" style="color:#dc2626;">Remove</button></form></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;" class="sw-grid">
    {{-- Setup / controls --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Setup &amp; Controls</div>

        <div style="font-size:13px;margin-bottom:6px;color:#6b7280;">Advertiser</div>
        <div style="margin-bottom:16px;font-weight:600;">{{ $softwareCampaign->user?->name }} <span style="font-weight:400;color:#9ca3af;">({{ $softwareCampaign->user?->email }})</span></div>

        <div style="font-size:13px;margin-bottom:6px;color:#6b7280;">Uploaded installer</div>
        <div style="margin-bottom:16px;">
            @if($softwareCampaign->payload_path)
                <div style="font-weight:600;">{{ $softwareCampaign->payload_original_name }}</div>
                <div style="font-size:12px;color:#9ca3af;margin-bottom:8px;">{{ number_format($softwareCampaign->payload_size / 1048576, 2) }} MB</div>
                <a href="{{ route('admin.software-campaigns.payload', $softwareCampaign) }}" class="btn btn-ghost btn-sm">Download advertiser .exe</a>
            @else
                <span class="badge badge-danger">No installer uploaded</span>
            @endif
        </div>

        {{-- Counting mode --}}
        <form method="POST" action="{{ route('admin.software-campaigns.mode', $softwareCampaign) }}" style="margin-bottom:16px;border-top:1px solid #f3f4f6;padding-top:16px;">
            @csrf
            <div style="font-size:13px;margin-bottom:8px;color:#6b7280;">How to count an install</div>
            <label style="display:block;font-size:13px;margin-bottom:6px;cursor:pointer;">
                <input type="radio" name="install_mode" value="install" {{ $softwareCampaign->install_mode==='install'?'checked':'' }}>
                <strong>On install</strong> — counted only after the setup actually completes installing.
            </label>
            <label style="display:block;font-size:13px;margin-bottom:10px;cursor:pointer;">
                <input type="radio" name="install_mode" value="open" {{ $softwareCampaign->install_mode==='open'?'checked':'' }}>
                <strong>On open</strong> — counted instantly the moment the user runs the setup.
            </label>
            <button class="btn btn-primary btn-sm">Save mode</button>
        </form>
    </div>

    {{-- Distribution --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Distribution Link</div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:12px;">
            Drive traffic to this link. It serves the stub with a unique per-download token baked into the
            filename, so every install is attributed back to this campaign.
        </p>
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;font-family:monospace;font-size:13px;word-break:break-all;margin-bottom:12px;">
            {{ url($downloadUrl) }}
        </div>
        <button class="btn btn-ghost btn-sm" onclick="navigator.clipboard.writeText('{{ url($downloadUrl) }}');this.textContent='Copied!';">Copy link</button>
        <div style="font-size:11px;color:#9ca3af;margin-top:8px;">Tip: swap the domain for any of your tracking domains — keep <code>/app/dl/{{ $softwareCampaign->code }}</code>. Install reports still come back here.</div>

        {{-- Download filename --}}
        <form method="POST" action="{{ route('admin.software-campaigns.download-name', $softwareCampaign) }}" style="border-top:1px solid #f3f4f6;margin-top:16px;padding-top:16px;">
            @csrf
            <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Download file name</div>
            <input type="text" name="download_name" class="form-control" value="{{ $softwareCampaign->download_name }}" placeholder="e.g. Font-Manager-Setup" style="margin-bottom:6px;">
            <div style="font-size:11px;color:#9ca3af;margin-bottom:8px;">
                Users will receive: <code>{{ $softwareCampaign->downloadBaseName() }}_xxxxxxxx.exe</code>
                <br>Only the short tag at the end changes per download (needed to track the install). Letters, numbers and hyphens only.
            </div>
            <button class="btn btn-ghost btn-sm">Save name</button>
        </form>

        <div style="border-top:1px solid #f3f4f6;margin-top:16px;padding-top:16px;">
            <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Campaign code</div>
            <code style="font-size:14px;">{{ $softwareCampaign->code }}</code>
        </div>

        <div style="border-top:1px solid #f3f4f6;margin-top:16px;padding-top:16px;">
            <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Geo targeting (set by advertiser)</div>
            <span class="badge badge-info">{{ $softwareCampaign->geoModeLabel() }}</span>
            @if(!empty($softwareCampaign->excluded_countries))
            <span style="font-size:12px;color:#dc2626;margin-left:6px;">excluding {{ implode(', ', $softwareCampaign->excluded_countries) }}</span>
            @endif
            <div style="margin-top:10px;">
                <a href="{{ route('admin.software-links.create') }}" class="btn btn-ghost btn-sm">Create a geo-gated link →</a>
            </div>
        </div>

        {{-- Admin note --}}
        <form method="POST" action="{{ route('admin.software-campaigns.note', $softwareCampaign) }}" style="border-top:1px solid #f3f4f6;margin-top:16px;padding-top:16px;">
            @csrf
            <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Internal note</div>
            <textarea name="admin_note" class="form-control" rows="3" style="margin-bottom:8px;">{{ $softwareCampaign->admin_note }}</textarea>
            <button class="btn btn-ghost btn-sm">Save note</button>
        </form>

        <form method="POST" action="{{ route('admin.software-campaigns.destroy', $softwareCampaign) }}" style="border-top:1px solid #f3f4f6;margin-top:16px;padding-top:16px;" onsubmit="return confirm('Delete this campaign and its uploaded installer? This cannot be undone.');">
            @csrf @method('DELETE')
            <button class="btn btn-ghost btn-sm" style="color:#dc2626;">Delete campaign</button>
        </form>
    </div>
</div>

{{-- Branding: per-campaign icon + branded stub --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-title" style="margin-bottom:8px;">Branding — Download Icon</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:16px;max-width:820px;">
        To show the advertiser's logo as the icon of the downloaded file, bake their <code>.ico</code> into a
        branded copy of the stub (built on your PC) and upload it here. This campaign then serves the branded
        stub; campaigns without one use the global stub.
    </p>

    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
        <span class="badge {{ $softwareCampaign->hasBrandedStub() ? 'badge-success' : 'badge-warning' }}">
            {{ $softwareCampaign->hasBrandedStub() ? 'Serving branded stub' : 'Using global stub' }}
        </span>
        <span class="badge {{ $softwareCampaign->hasIcon() ? 'badge-info' : 'badge-warning' }}">
            {{ $softwareCampaign->hasIcon() ? 'Icon uploaded' : 'No icon' }}
        </span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="sw-grid">
        <div>
            <div style="font-size:13px;font-weight:700;margin-bottom:8px;">1. Get the icon</div>
            @if($softwareCampaign->hasIcon())
                <div style="font-size:13px;margin-bottom:8px;">{{ $softwareCampaign->icon_original_name }}</div>
                <a href="{{ route('admin.software-campaigns.icon.download', $softwareCampaign) }}" class="btn btn-ghost btn-sm">Download .ico</a>
            @else
                <div style="font-size:13px;color:#9ca3af;margin-bottom:8px;">No icon uploaded by the advertiser. You can add one:</div>
            @endif
            <form method="POST" action="{{ route('admin.software-campaigns.icon.set', $softwareCampaign) }}" enctype="multipart/form-data" style="margin-top:10px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                @csrf
                <input type="file" name="icon" accept=".ico" class="form-control" style="width:220px;" required>
                <button class="btn btn-ghost btn-sm">{{ $softwareCampaign->hasIcon() ? 'Replace icon' : 'Upload icon' }}</button>
            </form>

            <div style="font-size:13px;font-weight:700;margin:18px 0 8px;">2. Build the branded stub (on your PC)</div>
            <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">Put the .ico in your <code>stub</code> folder, then run:</div>
            <div style="background:#0f172a;color:#e2e8f0;border-radius:8px;padding:10px 12px;font-family:monospace;font-size:12px;word-break:break-all;">
                dotnet publish stub.csproj -c Release -r win-x64 -o out -p:PublishTrimmed=true -p:TrimMode=partial -p:StubIcon={{ $softwareCampaign->icon_original_name ?: 'youricon.ico' }}
            </div>
        </div>

        <div>
            <div style="font-size:13px;font-weight:700;margin-bottom:8px;">3. Upload the branded stub</div>
            <form method="POST" action="{{ route('admin.software-campaigns.campaign-stub', $softwareCampaign) }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:12px;">
                @csrf
                <input type="file" name="stub" accept=".exe" class="form-control" style="width:240px;" required>
                <button class="btn btn-primary btn-sm">{{ $softwareCampaign->hasBrandedStub() ? 'Replace branded stub' : 'Upload branded stub' }}</button>
            </form>
            @if($softwareCampaign->hasBrandedStub())
            <form method="POST" action="{{ route('admin.software-campaigns.campaign-stub.remove', $softwareCampaign) }}" onsubmit="return confirm('Remove the branded stub? This campaign will fall back to the global stub.');">
                @csrf @method('DELETE')
                <button class="btn btn-ghost btn-sm" style="color:#dc2626;">Remove branded stub</button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- Installs table --}}
<div class="card">
    <div class="card-title" style="margin-bottom:16px;">Recent Installs</div>
    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>When</th><th>Region</th><th>IP</th><th>Device</th><th>OS</th><th>Opened</th><th>Installed</th></tr></thead>
            <tbody>
                @forelse($installs as $i)
                <tr>
                    <td style="font-size:12px;">{{ $i->created_at->format('M d, H:i') }}</td>
                    <td style="font-size:13px;">{{ $i->country_name ?: ($i->country_code ?: '—') }}</td>
                    <td style="font-size:12px;font-family:monospace;">{{ $i->ip_address ?: '—' }}</td>
                    <td style="font-size:13px;">{{ ucfirst($i->device_type ?: 'desktop') }}</td>
                    <td style="font-size:12px;">{{ trim(($i->os ?: '') . ' ' . ($i->os_version ?: '')) ?: '—' }}</td>
                    <td style="font-size:12px;">{{ $i->opened_at ? $i->opened_at->format('M d, H:i') : '—' }}</td>
                    <td style="font-size:12px;">{{ $i->installed_at ? $i->installed_at->format('M d, H:i') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:#9ca3af;">No installs recorded yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $installs->links() }}</div>
</div>

<style>@media(max-width:860px){.sw-grid{grid-template-columns:1fr !important;}}</style>
@endsection
