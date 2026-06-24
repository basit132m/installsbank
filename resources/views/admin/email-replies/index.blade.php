@extends('layouts.admin')
@section('title', 'Email Inbox')
@section('page-title', 'Email Inbox')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif

{{-- Header bar --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="display:flex;gap:6px;">
            <a href="?filter=all"
               style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;
                      {{ $filter==='all' ? 'background:#111827;color:#fff;' : 'background:#f3f4f6;color:#374151;' }}">
                All
            </a>
            <a href="?filter=unread"
               style="padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;
                      {{ $filter==='unread' ? 'background:#111827;color:#fff;' : 'background:#f3f4f6;color:#374151;' }}">
                Unread
                @if($unreadCount > 0)
                <span style="background:#ef4444;color:#fff;border-radius:20px;padding:1px 7px;font-size:11px;margin-left:4px;">{{ $unreadCount }}</span>
                @endif
            </a>
        </div>
        <div style="font-size:13px;color:#6b7280;">
            {{ $replies->total() }} {{ Str::plural('reply', $replies->total()) }}
        </div>
    </div>

    @if($unreadCount > 0)
    <form method="POST" action="{{ route('admin.email-replies.mark-all-read') }}" style="margin:0;">
        @csrf
        <button type="submit" style="background:#f3f4f6;border:none;border-radius:8px;padding:7px 16px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
            Mark All as Read
        </button>
    </form>
    @endif
</div>

{{-- Inbox list --}}
<div class="card" style="padding:0;overflow:hidden;">
    @if($replies->isEmpty())
    <div style="text-align:center;padding:64px 24px;color:#9ca3af;">
        <svg width="48" height="48" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 16px;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#6b7280;margin-bottom:6px;">No replies yet</div>
        <div style="font-size:13px;">Replies to broadcast emails will appear here automatically every 5 minutes.</div>
    </div>
    @else
    @foreach($replies as $reply)
    <a href="{{ route('admin.email-replies.show', $reply) }}"
       style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f3f4f6;text-decoration:none;
              transition:background .1s;{{ !$reply->is_read ? 'background:#fafafa;' : '' }}"
       onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='{{ !$reply->is_read ? '#fafafa' : '' }}'">

        {{-- Unread dot --}}
        <div style="width:8px;flex-shrink:0;">
            @if(!$reply->is_read)
            <div style="width:8px;height:8px;border-radius:50%;background:#3b82f6;"></div>
            @endif
        </div>

        {{-- Avatar --}}
        <div style="width:38px;height:38px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#374151;flex-shrink:0;">
            {{ strtoupper(substr($reply->from_name ?: $reply->from_email, 0, 1)) }}
        </div>

        {{-- Content --}}
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;">
                <div style="font-size:13px;font-weight:{{ $reply->is_read ? '500' : '700' }};color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;">
                    {{ $reply->from_name ?: $reply->from_email }}
                    @if($reply->from_name)
                    <span style="font-weight:400;color:#9ca3af;font-size:12px;">&lt;{{ $reply->from_email }}&gt;</span>
                    @endif
                </div>
                <div style="font-size:12px;color:#9ca3af;white-space:nowrap;flex-shrink:0;">
                    {{ $reply->received_at->diffForHumans() }}
                </div>
            </div>
            <div style="font-size:13px;color:#374151;font-weight:{{ $reply->is_read ? '400' : '600' }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">
                {{ $reply->subject }}
            </div>
            <div style="font-size:12px;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;">
                {{ Str::limit(str_replace("\n", ' ', $reply->body), 100) }}
            </div>
        </div>

        {{-- Matched badge --}}
        @if($reply->matched_log_id)
        <div style="flex-shrink:0;">
            <span style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:600;">
                Matched
            </span>
        </div>
        @endif

        {{-- Chevron --}}
        <svg width="16" height="16" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @endforeach

    @if($replies->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f3f4f6;">
        {{ $replies->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
