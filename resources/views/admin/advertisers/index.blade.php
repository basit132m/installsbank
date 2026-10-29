@extends('layouts.admin')
@section('title', 'Advertisers')
@section('page-title', 'Advertisers')

@section('content')

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="card-title">All Advertisers</div>
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="search" class="form-control" style="width:220px;" value="{{ request('search') }}" placeholder="Search name or email...">
            <button class="btn btn-ghost btn-sm">Search</button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead><tr><th>Advertiser</th><th>Company</th><th>Telegram / WhatsApp</th><th>Campaigns</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($advertisers as $adv)
                @php $profile = $adv->advertiserProfile; @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $adv->name }}</div>
                        <div style="font-size:12px;color:#9ca3af;">{{ $adv->email }}</div>
                        <div style="font-size:11px;color:#9ca3af;">Joined {{ $adv->created_at->format('M d, Y') }}</div>
                    </td>
                    <td style="font-size:13px;">{{ $profile?->company_name ?? '—' }}</td>
                    <td style="font-size:12px;">
                        @if($profile?->telegram)<div>📱 {{ $profile->telegram }}</div>@endif
                        @if($profile?->whatsapp)<div>💬 {{ $profile->whatsapp }}</div>@endif
                    </td>
                    <td style="font-weight:600;">{{ $adv->software_campaigns_count }}</td>
                    <td><span class="badge {{ $adv->status==='active'?'badge-success':($adv->status==='suspended'?'badge-danger':'badge-warning') }}">{{ ucfirst($adv->status) }}</span></td>
                    <td><a href="{{ route('admin.advertisers.show', $adv) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:#9ca3af;">No advertisers yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $advertisers->links() }}</div>
</div>
@endsection
