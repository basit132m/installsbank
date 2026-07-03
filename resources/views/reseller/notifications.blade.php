@extends('layouts.reseller')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-title mb-4">All Notifications</div>

    @forelse($notifications as $n)
    <div style="display:flex;gap:12px;padding:14px 0;border-bottom:1px solid #f3f4f6;align-items:flex-start;">
        <div style="width:34px;height:34px;background:#f5f3ff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:14px;color:#374151;line-height:1.6;">{{ $n->message }}</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">{{ $n->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:#9ca3af;">
        <div style="font-size:40px;margin-bottom:10px;">🔔</div>
        No notifications yet
    </div>
    @endforelse

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
