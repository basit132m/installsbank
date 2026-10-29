@extends('layouts.admin')
@section('title', 'Software Installs')
@section('page-title', 'Software Install Campaigns')

@section('content')

{{-- Global stub binary --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-title" style="margin-bottom:8px;">Global Stub Binary</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:16px;max-width:760px;">
        This is the single lightweight wrapper we hand to users. On launch it reports the install back to us,
        downloads the advertiser's real installer from our server, and runs it — so the advertiser's signed
        .exe is never modified (keeps it clean of antivirus flags). Upload it once; every enabled campaign
        is served from it.
    </p>

    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <span class="badge {{ $stubReady ? 'badge-success' : 'badge-warning' }}">
            {{ $stubReady ? 'Stub ready' : 'No stub uploaded' }}
        </span>
        @if($stubReady)
            <span style="font-size:12px;color:#6b7280;">{{ number_format($stubSize / 1024, 1) }} KB</span>
        @endif

        <form method="POST" action="{{ route('admin.software-campaigns.stub') }}" enctype="multipart/form-data"
              style="display:flex;gap:8px;align-items:center;margin-left:auto;">
            @csrf
            <input type="file" name="stub" accept=".exe" class="form-control" style="width:260px;" required>
            <button class="btn btn-primary btn-sm">{{ $stubReady ? 'Replace stub' : 'Upload stub' }}</button>
        </form>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="card-title">All Campaigns</div>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="search" class="form-control" style="width:220px;" value="{{ request('search') }}" placeholder="Search name, code, advertiser...">
            <button class="btn btn-ghost btn-sm">Search</button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Campaign</th><th>Advertiser</th><th>Code</th><th>Rate</th><th>Installs / Target</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($campaigns as $c)
                @php
                    $live = $c->status === 'live';
                    $needs = in_array($c->status, ['pending_review','approved']);
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $c->name }}</div>
                        <div style="font-size:11px;color:#9ca3af;">Created {{ $c->created_at->format('M d, Y') }}
                            @unless($c->payload_path) · <span style="color:#dc2626;">no installer</span>@endunless
                        </div>
                    </td>
                    <td style="font-size:13px;">
                        <div>{{ $c->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $c->user?->email }}</div>
                    </td>
                    <td><code style="font-size:12px;">{{ $c->code }}</code></td>
                    <td style="font-size:13px;font-weight:600;">${{ number_format($c->effectiveRate(), 2) }}</td>
                    <td style="font-weight:600;color:#3b82f6;">{{ number_format($c->total_installs) }}<span style="color:#9ca3af;font-weight:500;">/{{ number_format($c->max_installs) }}</span></td>
                    <td><span class="badge {{ $c->statusBadgeClass() }}">{{ $c->statusLabel() }}</span></td>
                    <td><a href="{{ route('admin.software-campaigns.show', $c) }}" class="btn btn-ghost btn-sm">Manage</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:#9ca3af;">No campaigns yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $campaigns->links() }}</div>
</div>
@endsection
