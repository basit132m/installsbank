@extends('layouts.admin')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-title" style="margin-bottom:16px;">Recent Activity</div>
    @forelse($notifications as $n)
    <a href="{{ $n->link ?: '#' }}" style="display:block;text-decoration:none;color:inherit;padding:14px 0;border-bottom:1px solid #f3f4f6;">
        <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
                <div style="font-weight:600;font-size:14px;{{ $n->read_at ? '' : 'color:#1e40af;' }}">
                    @unless($n->read_at)<span style="display:inline-block;width:8px;height:8px;background:#3b82f6;border-radius:50%;margin-right:6px;"></span>@endunless
                    {{ $n->title }}
                </div>
                @if($n->body)<div style="font-size:13px;color:#6b7280;margin-top:3px;">{{ $n->body }}</div>@endif
            </div>
            <div style="font-size:12px;color:#9ca3af;white-space:nowrap;">{{ $n->created_at->diffForHumans() }}</div>
        </div>
    </a>
    @empty
    <div style="text-align:center;padding:40px;color:#9ca3af;">No notifications yet</div>
    @endforelse
    <div style="margin-top:16px;">{{ $notifications->links() }}</div>
</div>
@endsection
