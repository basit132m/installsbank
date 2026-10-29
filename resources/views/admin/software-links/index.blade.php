@extends('layouts.admin')
@section('title', 'Advertiser Links')
@section('page-title', 'Advertiser Install Links')

@section('content')

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="card-title">Install Links</div>
        <a href="{{ route('admin.software-links.create') }}" class="btn btn-primary btn-sm">+ Create Link</a>
    </div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:16px;max-width:820px;">
        These geo-gated links deliver an advertiser's installer. Visitors whose country matches the campaign's
        geo rules get the download; everyone else is sent to the link's fallback URL — so off-target installs
        never reach the advertiser.
    </p>

    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Link</th><th>Campaign</th><th>Geo</th><th>Fallback</th><th>Served / Off-geo</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($links as $link)
                @php $c = $link->campaign; @endphp
                <tr>
                    <td style="font-size:12px;">
                        <div style="font-weight:600;">{{ $link->name ?: $link->code }}</div>
                        <div style="font-family:monospace;color:#6b7280;word-break:break-all;">{{ url('/app/go/'.$link->code) }}</div>
                        <button class="btn btn-ghost btn-sm" style="margin-top:4px;" onclick="navigator.clipboard.writeText('{{ url('/app/go/'.$link->code) }}');this.textContent='Copied!';">Copy</button>
                    </td>
                    <td style="font-size:13px;">
                        <div>{{ $c?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#9ca3af;">{{ $c?->user?->name }}</div>
                    </td>
                    <td style="font-size:12px;">
                        <span class="badge badge-info">{{ $c?->geoModeLabel() }}</span>
                        @if($c && !empty($c->excluded_countries))
                        <div style="font-size:11px;color:#dc2626;margin-top:3px;">excl: {{ implode(', ', $c->excluded_countries) }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px;max-width:180px;word-break:break-all;">{{ $link->fallback_url ? \Illuminate\Support\Str::limit($link->fallback_url, 40) : '—' }}</td>
                    <td style="font-size:13px;"><strong style="color:#059669;">{{ number_format($link->served) }}</strong> / <span style="color:#9ca3af;">{{ number_format($link->rejected) }}</span></td>
                    <td><span class="badge {{ $link->is_active ? 'badge-success' : 'badge-warning' }}">{{ $link->is_active ? 'Active' : 'Paused' }}</span></td>
                    <td style="white-space:nowrap;">
                        <form method="POST" action="{{ route('admin.software-links.toggle', $link) }}" style="display:inline;">@csrf<button class="btn btn-ghost btn-sm">{{ $link->is_active ? 'Pause' : 'Activate' }}</button></form>
                        <form method="POST" action="{{ route('admin.software-links.destroy', $link) }}" style="display:inline;" onsubmit="return confirm('Delete this link?');">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm" style="color:#dc2626;">Delete</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:#9ca3af;">No links yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $links->links() }}</div>
</div>
@endsection
