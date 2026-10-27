@extends('layouts.admin')
@section('title', $softwareCampaign->name)
@section('page-title', 'Software Campaign — ' . $softwareCampaign->name)

@section('content')
@if(session('success'))
<div class="alert" style="background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;margin-bottom:20px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;margin-bottom:20px;">{{ session('error') }}</div>
@endif

<a href="{{ route('admin.software-campaigns.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">&larr; All campaigns</a>

@if(!$stubReady)
<div class="alert" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;margin-bottom:20px;">
    No global stub binary is uploaded yet. The download link will not serve until you upload a stub on the
    <a href="{{ route('admin.software-campaigns.index') }}" style="text-decoration:underline;">campaigns list</a>.
</div>
@endif

{{-- Stat row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:20px;">
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Installs Today</div><div style="font-size:28px;font-weight:800;color:#3b82f6;">{{ number_format($installsToday) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Total Installs</div><div style="font-size:28px;font-weight:800;">{{ number_format($installsTotal) }}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Status</div><div style="font-size:20px;font-weight:800;">{!! $softwareCampaign->enabled ? '<span style="color:#059669;">Live</span>' : '<span style="color:#d97706;">Paused</span>' !!}</div></div>
    <div class="card"><div style="font-size:12px;color:#9ca3af;">Counting Mode</div><div style="font-size:20px;font-weight:800;">{{ $softwareCampaign->install_mode==='open'?'On open':'On install' }}</div></div>
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

        {{-- Enable / pause --}}
        <form method="POST" action="{{ route('admin.software-campaigns.toggle', $softwareCampaign) }}" style="border-top:1px solid #f3f4f6;padding-top:16px;">
            @csrf
            @if($softwareCampaign->enabled)
                <button class="btn btn-ghost btn-sm" style="color:#d97706;">Pause campaign</button>
            @else
                <button class="btn btn-primary btn-sm">Enable (go live)</button>
            @endif
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

        <div style="border-top:1px solid #f3f4f6;margin-top:16px;padding-top:16px;">
            <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">Campaign code</div>
            <code style="font-size:14px;">{{ $softwareCampaign->code }}</code>
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
