@extends('layouts.publisher')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

<div style="max-width:720px;">

    {{-- Header row --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap;">
        <div>
            <div style="font-size:20px;font-weight:700;color:#111827;">All Notifications</div>
            <div style="font-size:13px;color:#6b7280;margin-top:2px;">Your latest updates from Installs Bank</div>
        </div>
        @if($notifications->total() > 0)
        <form method="POST" action="{{ route('publisher.notifications.read-all') }}">
            @csrf
            <button type="submit" style="background:#f3f4f6;border:none;cursor:pointer;padding:8px 14px;border-radius:8px;font-size:13px;color:#374151;font-weight:600;">
                Mark all as read
            </button>
        </form>
        @endif
    </div>

    {{-- Notification list --}}
    @forelse($notifications as $notif)
    <div style="display:flex;gap:14px;padding:16px;margin-bottom:8px;border-radius:12px;background:{{ $notif->read_at ? '#fff' : '#eff6ff' }};border:1px solid {{ $notif->read_at ? '#e5e7eb' : '#bfdbfe' }};align-items:flex-start;">

        {{-- Icon --}}
        <div style="flex-shrink:0;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;
            background:{{ $notif->type === 'domain_changed' ? '#dbeafe' : ($notif->type === 'warning' ? '#fef3c7' : '#dcfce7') }};
            color:{{ $notif->type === 'domain_changed' ? '#1d4ed8' : ($notif->type === 'warning' ? '#d97706' : '#16a34a') }};">
            @if($notif->type === 'domain_changed')
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
            @elseif($notif->type === 'warning')
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            @else
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            @endif
        </div>

        {{-- Message + meta --}}
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;color:#111827;line-height:1.5;white-space:pre-line;">{{ $notif->message }}</div>
            <div style="display:flex;align-items:center;gap:10px;margin-top:6px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#9ca3af;">{{ $notif->created_at->diffForHumans() }}</span>
                @if(!$notif->read_at)
                    <span style="font-size:11px;background:#2563eb;color:#fff;padding:2px 7px;border-radius:99px;font-weight:600;">New</span>
                @endif
            </div>
        </div>

        {{-- Mark read --}}
        @if(!$notif->read_at)
        <form method="POST" action="{{ route('publisher.notifications.read', $notif) }}">
            @csrf
            <button type="submit" title="Mark as read"
                    style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;flex-shrink:0;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </form>
        @endif
    </div>
    @empty
    <div style="text-align:center;padding:64px 24px;color:#9ca3af;">
        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 16px;display:block;opacity:.4;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#6b7280;">No notifications yet</div>
        <div style="font-size:13px;margin-top:4px;">You'll see updates from Installs Bank here.</div>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div style="margin-top:20px;">
        {{ $notifications->links() }}
    </div>
    @endif

</div>

@endsection
