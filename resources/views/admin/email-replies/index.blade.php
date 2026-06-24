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
        <div style="font-size:13px;color:#6b7280;">{{ $replies->total() }} {{ Str::plural('reply', $replies->total()) }}</div>
    </div>

    <div style="display:flex;gap:8px;align-items:center;">
        {{-- Bulk delete (shown when items selected) --}}
        <form method="POST" action="{{ route('admin.email-replies.bulk-delete') }}" id="bulkDeleteForm" style="display:none;margin:0;">
            @csrf
            <div id="bulkIdsContainer"></div>
            <button type="submit" onclick="return confirm('Delete selected emails? This cannot be undone.')"
                    style="background:#fff;border:1px solid #fca5a5;color:#ef4444;border-radius:8px;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete Selected (<span id="selectedCount">0</span>)
            </button>
        </form>

        @if($unreadCount > 0)
        <form method="POST" action="{{ route('admin.email-replies.mark-all-read') }}" style="margin:0;">
            @csrf
            <button type="submit" style="background:#f3f4f6;border:none;border-radius:8px;padding:7px 16px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;">
                Mark All as Read
            </button>
        </form>
        @endif
    </div>
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
    <div style="display:flex;align-items:center;border-bottom:1px solid #f3f4f6;{{ !$reply->is_read ? 'background:#fafafa;' : '' }}"
         id="row-{{ $reply->id }}">

        {{-- Checkbox --}}
        <div style="padding:0 4px 0 16px;flex-shrink:0;">
            <input type="checkbox" class="row-check" value="{{ $reply->id }}"
                   onclick="event.stopPropagation(); updateBulk()"
                   style="width:16px;height:16px;cursor:pointer;accent-color:#111827;">
        </div>

        <a href="{{ route('admin.email-replies.show', $reply) }}"
           style="display:flex;align-items:center;gap:14px;padding:14px 16px 14px 10px;text-decoration:none;flex:1;min-width:0;"
           onmouseover="this.parentElement.style.background='#f9fafb'"
           onmouseout="this.parentElement.style.background='{{ !$reply->is_read ? '#fafafa' : '' }}'">

            {{-- Unread dot --}}
            <div style="width:8px;flex-shrink:0;">
                @if(!$reply->is_read)
                <div style="width:8px;height:8px;border-radius:50%;background:#3b82f6;"></div>
                @endif
            </div>

            {{-- Avatar --}}
            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;flex-shrink:0;">
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

            @if($reply->matched_log_id)
            <span style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:600;flex-shrink:0;">
                Matched
            </span>
            @endif

            <svg width="16" height="16" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    @endforeach

    @if($replies->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f3f4f6;">
        {{ $replies->links() }}
    </div>
    @endif
    @endif
</div>

<script>
function updateBulk() {
    const checked = document.querySelectorAll('.row-check:checked');
    const form    = document.getElementById('bulkDeleteForm');
    const counter = document.getElementById('selectedCount');
    const container = document.getElementById('bulkIdsContainer');

    counter.textContent = checked.length;
    form.style.display  = checked.length > 0 ? 'block' : 'none';

    container.innerHTML = '';
    checked.forEach(cb => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
}
</script>
@endsection
