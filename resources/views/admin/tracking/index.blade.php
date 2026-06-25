@extends('layouts.admin')
@section('title', 'Tracking Links')
@section('page-title', 'Tracking Links')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;display:flex;align-items:center;gap:10px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ═══════════════════════════════════════════════
     HERO STRIP
     ═══════════════════════════════════════════════ --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:20px;padding:24px 32px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;right:-50px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(1,191,99,.15) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-30px;left:35%;width:150px;height:150px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.12) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;position:relative;">
        <div style="flex:1;min-width:180px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#64748b;margin-bottom:6px;">Traffic Infrastructure</div>
            <div style="font-size:24px;font-weight:900;color:#f8fafc;letter-spacing:-0.5px;line-height:1.1;">Tracking Links</div>
            <div style="font-size:13px;color:#64748b;margin-top:4px;">Manage links — assign publishers, swap domains, toggle status</div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;flex-shrink:0;align-items:stretch;">
            <div style="background:rgba(1,191,99,.12);border:1px solid rgba(1,191,99,.25);border-radius:14px;padding:12px 20px;text-align:center;min-width:100px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#6ee7b7;margin-bottom:3px;">Total</div>
                <div style="font-size:24px;font-weight:900;color:#01BF63;line-height:1;">{{ $links->total() }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">links</div>
            </div>
            <div style="background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.25);border-radius:14px;padding:12px 20px;text-align:center;min-width:100px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#93c5fd;margin-bottom:3px;">Active</div>
                <div style="font-size:24px;font-weight:900;color:#60a5fa;line-height:1;">{{ $links->where('is_active', true)->count() }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">running</div>
            </div>
            <a href="{{ route('admin.tracking.create') }}"
               style="display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#01BF63,#059669);border-radius:14px;padding:12px 24px;text-decoration:none;box-shadow:0 4px 16px rgba(1,191,99,.4);min-width:110px;transition:transform .15s;"
               onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.5" style="margin-bottom:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <div style="font-size:13px;font-weight:800;color:white;">Create Link</div>
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     ACTIVITY LEGEND
     ═══════════════════════════════════════════════ --}}
<div style="display:flex;gap:8px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
    <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;">Ad Code Activity:</span>
    @foreach([
        ['#10b981','#d1fae5','#065f46','Active (within 2h)'],
        ['#f59e0b','#fef3c7','#92400e','Idle (2–6h)'],
        ['#ef4444','#fee2e2','#991b1b','Inactive (6h+)'],
        ['#9ca3af','#f3f4f6','#6b7280','No clicks yet'],
    ] as [$dot, $bg, $text, $label])
    <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $bg }};color:{{ $text }};border:1px solid {{ $dot }}30;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;">
        <span style="width:7px;height:7px;border-radius:50%;background:{{ $dot }};display:inline-block;flex-shrink:0;"></span>
        {{ $label }}
    </span>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════
     LINKS TABLE
     ═══════════════════════════════════════════════ --}}
<div class="card" style="padding:0;overflow:hidden;border:1.5px solid #e5e7eb;">

    {{-- Table header bar --}}
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(59,130,246,.25);">
            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        <div>
            <div style="font-size:14px;font-weight:800;color:#111827;">All Tracking Links</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">{{ $links->total() }} total — newest first</div>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:900px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Publisher</th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Link</th>
                    <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Destination</th>
                    <th style="text-align:center;padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Clicks</th>
                    <th style="text-align:center;padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Activity</th>
                    <th style="text-align:center;padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                    <th style="text-align:right;padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                $avatarGrads = [
                    'linear-gradient(135deg,#01BF63,#059669)',
                    'linear-gradient(135deg,#3b82f6,#1d4ed8)',
                    'linear-gradient(135deg,#8b5cf6,#6d28d9)',
                    'linear-gradient(135deg,#f59e0b,#d97706)',
                    'linear-gradient(135deg,#ec4899,#db2777)',
                    'linear-gradient(135deg,#06b6d4,#0891b2)',
                ];
                @endphp

                @forelse($links as $link)
                @php
                    $lastClick  = $link->last_click_at;
                    $hoursSince = $lastClick ? now()->diffInHours($lastClick) : null;
                    if ($lastClick === null) {
                        $adDot = '#9ca3af'; $adLabel = 'No clicks'; $adBg = '#f3f4f6'; $adText = '#6b7280';
                    } elseif ($hoursSince <= 2) {
                        $adDot = '#10b981'; $adLabel = 'Active';    $adBg = '#d1fae5'; $adText = '#065f46';
                    } elseif ($hoursSince <= 6) {
                        $adDot = '#f59e0b'; $adLabel = 'Idle';      $adBg = '#fef3c7'; $adText = '#92400e';
                    } else {
                        $adDot = '#ef4444'; $adLabel = 'Inactive';  $adBg = '#fee2e2'; $adText = '#991b1b';
                    }
                    $initials = collect(explode(' ', $link->user->name ?? '?'))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                    $grad = $avatarGrads[crc32($link->user->name ?? '') % count($avatarGrads)];
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">

                    {{-- Publisher --}}
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:10px;background:{{ $grad }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900;color:white;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                                {{ $initials }}
                            </div>
                            <div>
                                <a href="{{ route('admin.publishers.show', $link->user) }}"
                                   style="font-size:13px;font-weight:700;color:#111827;text-decoration:none;"
                                   onmouseover="this.style.color='#01BF63'" onmouseout="this.style.color='#111827'">
                                    {{ $link->user->name }}
                                </a>
                                <div style="font-size:11px;color:#9ca3af;margin-top:1px;">{{ $link->user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Link name + code --}}
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <div style="font-size:13px;font-weight:700;color:#111827;">{{ $link->name ?: 'Unnamed Link' }}</div>
                        <div style="display:flex;align-items:center;gap:5px;margin-top:4px;flex-wrap:wrap;">
                            <code style="font-size:11px;background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:6px;font-weight:700;letter-spacing:0.04em;">{{ $link->unique_code }}</code>
                            @if($link->allowed_domain)
                            <span style="font-size:10px;background:#eff6ff;color:#1d4ed8;padding:2px 7px;border-radius:5px;font-weight:700;border:1px solid #bfdbfe;">
                                🔒 {{ $link->allowed_domain }}
                            </span>
                            @endif
                        </div>
                        <div style="font-size:10px;color:#9ca3af;margin-top:4px;font-family:monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;">
                            {{ $link->tracking_url }}
                        </div>
                    </td>

                    {{-- Destination --}}
                    <td style="padding:14px 16px;vertical-align:middle;max-width:160px;">
                        <a href="{{ $link->original_url }}" target="_blank"
                           style="font-size:12px;color:#6b7280;text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                           title="{{ $link->original_url }}"
                           onmouseover="this.style.color='#01BF63'" onmouseout="this.style.color='#6b7280'">
                            {{ $link->original_url }}
                        </a>
                        @if($link->url_windows || $link->url_android || $link->url_mac)
                        <div style="display:flex;gap:4px;margin-top:5px;flex-wrap:wrap;">
                            @if($link->url_windows)<span style="font-size:10px;background:#dbeafe;color:#1e40af;padding:2px 6px;border-radius:4px;font-weight:700;border:1px solid #bfdbfe;">WIN</span>@endif
                            @if($link->url_android)<span style="font-size:10px;background:#d1fae5;color:#065f46;padding:2px 6px;border-radius:4px;font-weight:700;border:1px solid #6ee7b7;">AND</span>@endif
                            @if($link->url_mac)<span style="font-size:10px;background:#f3f4f6;color:#374151;padding:2px 6px;border-radius:4px;font-weight:700;border:1px solid #e5e7eb;">MAC</span>@endif
                        </div>
                        @endif
                    </td>

                    {{-- Clicks --}}
                    <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                        <div style="font-size:18px;font-weight:900;color:#111827;line-height:1;">{{ number_format($link->unique_clicks) }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:2px;">valid clicks</div>
                        @if($link->fraud_clicks > 0)
                        <div style="font-size:11px;color:#ef4444;font-weight:700;margin-top:1px;">{{ number_format($link->fraud_clicks) }} fraud</div>
                        @endif
                    </td>

                    {{-- Activity --}}
                    <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $adBg }};color:{{ $adText }};border:1px solid {{ $adDot }}30;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $adDot }};display:inline-block;{{ $adLabel === 'Active' ? 'box-shadow:0 0 5px '.$adDot.';' : '' }}"></span>
                            {{ $adLabel }}
                        </span>
                        @if($lastClick)
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">{{ $lastClick->format('M d, H:i') }}</div>
                        @endif
                    </td>

                    {{-- Status + Domain --}}
                    <td style="padding:14px 16px;vertical-align:middle;text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:4px;background:{{ $link->is_active ? '#f0fdf4' : '#fef2f2' }};color:{{ $link->is_active ? '#166534' : '#991b1b' }};border:1px solid {{ $link->is_active ? '#bbf7d0' : '#fecaca' }};border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">
                            <span style="width:5px;height:5px;border-radius:50%;background:{{ $link->is_active ? '#10b981' : '#ef4444' }};display:inline-block;"></span>
                            {{ $link->is_active ? 'Active' : 'Paused' }}
                        </span>
                        <div style="display:inline-flex;align-items:center;gap:4px;margin-top:5px;">
                            <svg width="10" height="10" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            <span style="font-size:11px;color:#6b7280;font-weight:600;">{{ $link->trackingDomain?->domain ?? 'default' }}</span>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <div style="display:flex;gap:5px;justify-content:flex-end;flex-wrap:wrap;align-items:center;">
                            <button onclick="openSwapDomain({{ $link->id }}, '{{ addslashes($link->unique_code) }}', '{{ addslashes($link->name ?: 'Unnamed Link') }}', {{ $link->tracking_domain_id ?? 'null' }})"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#faf5ff;color:#7c3aed;border:1px solid #e9d5ff;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;"
                                    onmouseover="this.style.background='#f5f3ff'" onmouseout="this.style.background='#faf5ff'">
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                                Domain
                            </button>
                            <button onclick="openReassign({{ $link->id }}, '{{ addslashes($link->unique_code) }}', '{{ addslashes($link->name ?: 'Unnamed Link') }}', {{ $link->user_id }})"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;"
                                    onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Reassign
                            </button>
                            <a href="{{ route('admin.tracking.edit', $link) }}"
                               style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:#f9fafb;color:#374151;border:1px solid #e5e7eb;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;"
                               onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.tracking.toggle', $link) }}" style="margin:0;">
                                @csrf
                                <button style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;background:{{ $link->is_active ? '#fffbeb' : '#f0fdf4' }};color:{{ $link->is_active ? '#92400e' : '#065f46' }};border:1px solid {{ $link->is_active ? '#fde68a' : '#bbf7d0' }};border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;">
                                    {{ $link->is_active ? 'Pause' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.tracking.destroy', $link) }}" style="margin:0;"
                                  onsubmit="return confirm('Delete link {{ $link->unique_code }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:7px;cursor:pointer;"
                                        onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'"
                                        title="Delete">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:56px 24px;color:#9ca3af;">
                        <svg width="40" height="40" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 12px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <div style="font-size:15px;font-weight:700;color:#6b7280;margin-bottom:6px;">No tracking links yet</div>
                        <a href="{{ route('admin.tracking.create') }}" style="font-size:13px;color:#01BF63;font-weight:600;text-decoration:none;">+ Create your first link →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($links->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f3f4f6;">
        {{ $links->links() }}
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════
     REASSIGN PUBLISHER MODAL
     ═══════════════════════════════════════════════ --}}
<div id="reassignModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:18px;padding:28px;width:100%;max-width:460px;margin:16px;box-shadow:0 24px 80px rgba(0,0,0,.25);border:1px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:22px;">
            <div style="width:42px;height:42px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(59,130,246,.3);">
                <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:16px;font-weight:800;color:#111827;">Reassign Publisher</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Move this link to a different publisher account</div>
            </div>
            <button onclick="closeReassign()" style="width:32px;height:32px;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb;cursor:pointer;color:#9ca3af;display:flex;align-items:center;justify-content:center;font-size:18px;line-height:1;">×</button>
        </div>

        <div style="background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:12px;padding:14px;margin-bottom:20px;">
            <div style="font-size:11px;color:#9ca3af;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Link being reassigned</div>
            <div style="font-size:14px;font-weight:800;color:#111827;" id="modalLinkName"></div>
            <code style="font-size:12px;color:#6b7280;background:#ede9fe;padding:2px 8px;border-radius:5px;" id="modalLinkCode"></code>
        </div>

        <form id="reassignForm" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Assign to Publisher</label>
                <select name="user_id" id="reassignSelect" class="form-control form-select">
                    @foreach($publishers as $pub)
                    <option value="{{ $pub->id }}" data-email="{{ $pub->email }}">{{ $pub->name }} — {{ $pub->email }}</option>
                    @endforeach
                </select>
                <div style="font-size:12px;color:#9ca3af;margin-top:6px;" id="currentlyAssigned"></div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="flex:1;padding:12px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(59,130,246,.3);">
                    Confirm Reassign
                </button>
                <button type="button" onclick="closeReassign()"
                        style="padding:12px 20px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     SWAP DOMAIN MODAL
     ═══════════════════════════════════════════════ --}}
<div id="swapDomainModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:18px;padding:28px;width:100%;max-width:480px;margin:16px;box-shadow:0 24px 80px rgba(0,0,0,.25);border:1px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:22px;">
            <div style="width:42px;height:42px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(124,58,237,.3);">
                <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
            </div>
            <div style="flex:1;">
                <div style="font-size:16px;font-weight:800;color:#111827;">Change Tracking Domain</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Swap domain — the code stays the same</div>
            </div>
            <button onclick="closeSwapDomain()" style="width:32px;height:32px;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb;cursor:pointer;color:#9ca3af;display:flex;align-items:center;justify-content:center;font-size:18px;line-height:1;">×</button>
        </div>

        <div style="background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:12px;padding:14px;margin-bottom:18px;">
            <div style="font-size:11px;color:#9ca3af;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Link</div>
            <div style="font-size:14px;font-weight:800;color:#111827;" id="sdModalLinkName"></div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:6px;flex-wrap:wrap;">
                <code style="font-size:12px;color:#374151;background:#ede9fe;padding:2px 8px;border-radius:5px;" id="sdModalLinkCode"></code>
                <svg width="12" height="12" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                <span style="font-size:12px;color:#7c3aed;font-weight:700;" id="sdModalCurrentDomain"></span>
            </div>
        </div>

        <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:10px;padding:12px 14px;margin-bottom:18px;font-size:12px;color:#6b21a8;line-height:1.7;">
            <strong>How it works:</strong> The tracking code (<code id="sdModalCode2" style="background:#ede9fe;padding:1px 6px;border-radius:4px;"></code>) stays unchanged. Only the domain prefix changes. Takes effect immediately.
        </div>

        <form id="swapDomainForm" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">New Domain</label>
                <select name="tracking_domain_id" id="swapDomainSelect" class="form-control form-select" onchange="updateSwapPreview()">
                    <option value="">— Default (installsbank.com) —</option>
                    @foreach($domains as $domain)
                    <option value="{{ $domain->id }}" data-url="{{ $domain->base_url }}">
                        {{ $domain->domain }}{{ $domain->label ? ' — ' . $domain->label : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:10px;padding:12px 14px;margin-bottom:18px;">
                <div style="font-size:11px;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:4px;">New tracking URL:</div>
                <div style="font-size:12px;font-family:monospace;color:#047857;word-break:break-all;" id="sdUrlPreview"></div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit"
                        style="flex:1;padding:12px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(124,58,237,.3);">
                    Apply Domain Change
                </button>
                <button type="button" onclick="closeSwapDomain()"
                        style="padding:12px 20px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openReassign(linkId, code, name, currentUserId) {
    document.getElementById('modalLinkName').textContent = name;
    document.getElementById('modalLinkCode').textContent  = code;
    document.getElementById('reassignForm').action = '/admin/tracking/' + linkId + '/reassign';
    const select = document.getElementById('reassignSelect');
    for (let opt of select.options) opt.selected = (parseInt(opt.value) === currentUserId);
    updateCurrentLabel();
    document.getElementById('reassignModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeReassign() {
    document.getElementById('reassignModal').style.display = 'none';
    document.body.style.overflow = '';
}
function updateCurrentLabel() {
    const opt = document.getElementById('reassignSelect').options[document.getElementById('reassignSelect').selectedIndex];
    document.getElementById('currentlyAssigned').textContent = opt ? 'Selected: ' + opt.text : '';
}
document.getElementById('reassignSelect').addEventListener('change', updateCurrentLabel);
document.getElementById('reassignModal').addEventListener('click', function(e) { if (e.target === this) closeReassign(); });

let sdLinkCode = '';
function openSwapDomain(linkId, code, name, currentDomainId) {
    sdLinkCode = code;
    document.getElementById('sdModalLinkName').textContent = name;
    document.getElementById('sdModalLinkCode').textContent = code;
    document.getElementById('sdModalCode2').textContent    = code;
    document.getElementById('swapDomainForm').action       = '/admin/tracking/' + linkId + '/swap-domain';
    const select = document.getElementById('swapDomainSelect');
    for (let opt of select.options) opt.selected = currentDomainId && parseInt(opt.value) === currentDomainId;
    if (!currentDomainId) select.selectedIndex = 0;
    updateSwapPreview();
    document.getElementById('swapDomainModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeSwapDomain() {
    document.getElementById('swapDomainModal').style.display = 'none';
    document.body.style.overflow = '';
}
function updateSwapPreview() {
    const select  = document.getElementById('swapDomainSelect');
    const opt     = select.options[select.selectedIndex];
    const baseUrl = opt && opt.dataset.url ? opt.dataset.url : window.location.origin;
    document.getElementById('sdModalCurrentDomain').textContent = opt && opt.value ? opt.text.split(' —')[0] : 'default (installsbank.com)';
    document.getElementById('sdUrlPreview').textContent = baseUrl + '/track/' + sdLinkCode;
}
document.getElementById('swapDomainSelect').addEventListener('change', updateSwapPreview);
document.getElementById('swapDomainModal').addEventListener('click', function(e) { if (e.target === this) closeSwapDomain(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeReassign(); closeSwapDomain(); } });
</script>
@endpush
