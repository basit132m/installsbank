@extends('layouts.admin')
@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
@php
    $statusColor = match($user->status) {
        'active'    => ['bg'=>'#d1fae5','text'=>'#065f46','dot'=>'#10b981'],
        'pending'   => ['bg'=>'#fef3c7','text'=>'#92400e','dot'=>'#f59e0b'],
        'suspended' => ['bg'=>'#fee2e2','text'=>'#991b1b','dot'=>'#ef4444'],
        default     => ['bg'=>'#f3f4f6','text'=>'#374151','dot'=>'#9ca3af'],
    };
    $contractType = $user->publisherProfile?->contract_type ?? 'none';
    $contractLabel = match($contractType) {
        'per_click'     => 'Per Click',
        'fixed'         => 'Fixed Daily',
        'installs_base' => 'Installs Based',
        default         => 'No Contract',
    };
    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
    $tagColors = ['green'=>'#01BF63','blue'=>'#3b82f6','red'=>'#ef4444','amber'=>'#f59e0b','gray'=>'#6b7280','purple'=>'#8b5cf6'];
@endphp

<!-- Publisher Hero Card -->
<div class="card mb-5" style="border:1.5px solid #e5e7eb;padding:0;overflow:hidden;">
    <!-- Top accent bar -->
    <div style="height:4px;background:linear-gradient(90deg,#01BF63,#3b82f6);"></div>
    <div style="padding:24px 28px;">
        <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">

            <!-- Avatar + Name block -->
            <div style="display:flex;gap:16px;align-items:center;flex:1;min-width:240px;">
                <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#01BF63,#059669);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px;font-weight:900;color:white;letter-spacing:-1px;">
                    {{ $initials }}
                </div>
                <div>
                    <div style="font-size:20px;font-weight:800;color:#111827;line-height:1.2;margin-bottom:4px;">{{ $user->name }}</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                        <!-- Status pill -->
                        <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $statusColor['dot'] }};display:inline-block;"></span>
                            {{ ucfirst($user->status) }}
                        </span>
                        <!-- Contract pill -->
                        <span style="display:inline-flex;align-items:center;background:#eff6ff;color:#1d4ed8;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                            {{ $contractLabel }}
                        </span>
                        <!-- Payment pill -->
                        @if($user->publisherProfile?->payment_enabled)
                        <span style="display:inline-flex;align-items:center;background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                            Payments On
                        </span>
                        @else
                        <span style="display:inline-flex;align-items:center;background:#f3f4f6;color:#6b7280;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                            Payments Off
                        </span>
                        @endif
                        @if($user->publisherProfile?->adcode_requested_at)
                        <span style="display:inline-flex;align-items:center;background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;" title="Requested {{ $user->publisherProfile->adcode_requested_at->format('M d, Y H:i') }}">
                            Adcode Pending
                        </span>
                        @endif
                        <!-- Tags -->
                        @foreach($user->publisherTags as $tag)
                        @php $tc = $tagColors[$tag->color] ?? '#6b7280'; @endphp
                        <span style="display:inline-flex;align-items:center;background:{{ $tc }}1a;color:{{ $tc }};border:1px solid {{ $tc }}40;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                            {{ $tag->tag }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Balance / Earnings -->
            <div style="display:flex;gap:12px;flex-shrink:0;flex-wrap:wrap;">
                <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:12px 18px;text-align:center;min-width:110px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#059669;margin-bottom:2px;">Balance</div>
                    <div style="font-size:20px;font-weight:900;color:#059669;">${{ number_format($user->publisherProfile?->balance ?? 0, 2) }}</div>
                </div>
                <div style="background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:12px;padding:12px 18px;text-align:center;min-width:110px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;margin-bottom:2px;">Total Earned</div>
                    <div style="font-size:20px;font-weight:900;color:#374151;">${{ number_format($user->publisherProfile?->total_earnings ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Contact info row -->
        <div style="display:flex;flex-wrap:wrap;gap:20px;margin-top:18px;padding-top:18px;border-top:1px solid #f3f4f6;">
            <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#374151;">
                <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span style="color:#6b7280;">{{ $user->email }}</span>
            </div>
            @if($user->phone)
            <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#374151;">
                <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span style="color:#6b7280;">{{ $user->phone }}</span>
            </div>
            @endif
            @if($user->telegram)
            <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#374151;">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.085 13.86l-2.96-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.834.7z"/></svg>
                <span style="color:#6b7280;">{{ $user->telegram }}</span>
            </div>
            @endif
            @if($user->website)
            <div style="display:flex;align-items:center;gap:7px;font-size:13px;">
                <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <a href="{{ $user->website }}" target="_blank" style="color:#01BF63;font-weight:600;">{{ parse_url($user->website, PHP_URL_HOST) ?: $user->website }}</a>
            </div>
            @endif
            <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#9ca3af;">
                <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Joined {{ $user->created_at->format('M d, Y') }}
            </div>
            <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:#9ca3af;">
                <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Last login {{ $user->last_login_at?->diffForHumans() ?? 'never' }}
            </div>
        </div>

        <!-- Action buttons row -->
        <div style="display:flex;gap:8px;align-items:center;margin-top:18px;padding-top:16px;border-top:1px solid #f3f4f6;flex-wrap:wrap;">
            <a href="{{ route('admin.publishers.index') }}" class="btn btn-ghost btn-sm">← All Publishers</a>
            <a href="{{ route('admin.publishers.stats', $user) }}" class="btn btn-ghost btn-sm" style="color:#3b82f6;border-color:#bfdbfe;background:#eff6ff;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="margin-right:5px;vertical-align:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Detailed Stats
            </a>
            @if($user->status === 'pending')
                <form method="POST" action="{{ route('admin.publishers.activate', $user) }}" style="display:inline;">@csrf
                    <button class="btn btn-success btn-sm">Activate Publisher</button>
                </form>
            @elseif($user->status === 'active')
                <form method="POST" action="{{ route('admin.publishers.suspend', $user) }}" style="display:inline;" onsubmit="return confirm('Suspend this publisher?')">@csrf
                    <button class="btn btn-danger btn-sm">Suspend</button>
                </form>
            @elseif($user->status === 'suspended')
                <form method="POST" action="{{ route('admin.publishers.activate', $user) }}" style="display:inline;" onsubmit="return confirm('Reactivate this publisher?')">@csrf
                    <button class="btn btn-success btn-sm">Reactivate Publisher</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.publishers.destroy', $user) }}" style="display:inline;margin-left:auto;"
                  onsubmit="return confirm('DELETE {{ addslashes($user->name) }}?\n\nThis permanently deletes ALL data including clicks, earnings, tracking links, withdrawals, and fraud alerts.\n\nThis cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" style="background:#fee2e2;color:#991b1b;border-color:#fca5a5;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="margin-right:4px;vertical-align:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete Publisher
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Screenshots (if any) -->
@if($user->stat_screenshots && count($user->stat_screenshots) > 0)
<div class="card mb-5">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
        <div style="width:32px;height:32px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <div style="font-size:14px;font-weight:700;color:#111827;">Submitted Statistics Screenshots</div>
            <div style="font-size:12px;color:#6b7280;">{{ count($user->stat_screenshots) }} file(s) submitted with application</div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;">
        @foreach($user->stat_screenshots as $i => $path)
        <a href="{{ asset('storage/' . $path) }}" target="_blank"
           style="display:block;border-radius:10px;overflow:hidden;border:1.5px solid #e5e7eb;transition:all 0.15s;"
           onmouseover="this.style.borderColor='#01BF63';this.style.transform='translateY(-1px)'" onmouseout="this.style.borderColor='#e5e7eb';this.style.transform=''">
            <img src="{{ asset('storage/' . $path) }}" alt="Screenshot {{ $i+1 }}"
                 style="width:100%;height:95px;object-fit:cover;display:block;">
            <div style="background:#f9fafb;padding:5px 8px;font-size:11px;color:#6b7280;font-weight:600;text-align:center;">
                Screenshot {{ $i+1 }}
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- Click Stats Strip -->
@php
    $statCards = [
        ['label'=>'Valid Clicks Today',        'value'=>number_format($clickStats['today']),                'color'=>'#01BF63','bg'=>'#f0fdf4','border'=>'#bbf7d0','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'This Week',                 'value'=>number_format($clickStats['this_week']),            'color'=>'#3b82f6','bg'=>'#eff6ff','border'=>'#bfdbfe','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label'=>'All Time Valid',            'value'=>number_format($clickStats['total']),                'color'=>'#111827','bg'=>'#f9fafb','border'=>'#e5e7eb','icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ['label'=>'Windows Today (Shown)',     'value'=>number_format($clickStats['today_windows']),        'color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a','icon'=>'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['label'=>'Actual Windows (Raw)',      'value'=>number_format($clickStats['total_actual_windows']), 'color'=>'#ea580c','bg'=>'#fff7ed','border'=>'#fed7aa','icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
        ['label'=>'Fraud Clicks Today',        'value'=>number_format($clickStats['today_fraud']),          'color'=>'#ef4444','bg'=>'#fef2f2','border'=>'#fecaca','icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
    ];
@endphp
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
    @foreach($statCards as $sc)
    <div style="background:{{ $sc['bg'] }};border:1.5px solid {{ $sc['border'] }};border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;">
        <div style="width:40px;height:40px;background:white;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <svg width="18" height="18" fill="none" stroke="{{ $sc['color'] }}" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['icon'] }}"/></svg>
        </div>
        <div>
            <div style="font-size:22px;font-weight:800;color:{{ $sc['color'] }};line-height:1;">{{ $sc['value'] }}</div>
            <div style="font-size:12px;color:#6b7280;margin-top:2px;">{{ $sc['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<!-- Install Earnings Comparison (installs_base only) -->
@if($installStats)
<div class="card mb-6" style="border:1.5px solid #ede9fe;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
        <div style="width:38px;height:38px;background:#ede9fe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:2px;">Install Earnings — Today</div>
            <div style="font-size:12px;color:#6b7280;">Divider: <strong>{{ $installStats['divider_value'] }}×</strong> &nbsp;·&nbsp; Clicks per install (base ratio): <strong>{{ $installStats['ratio'] }}</strong> &nbsp;·&nbsp; Effective threshold: <strong>{{ (int)round($installStats['ratio'] * $installStats['divider_value']) }} raw clicks</strong></div>
        </div>
        <span style="margin-left:auto;background:#ede9fe;color:#7c3aed;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">Installs Base</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
        {{-- Publisher View --}}
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:16px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#059669;margin-bottom:10px;">Publisher Sees (Divider Applied)</div>
            <div style="display:flex;gap:16px;">
                <div>
                    <div style="font-size:26px;font-weight:900;color:#059669;">{{ number_format($installStats['publisher_installs']) }}</div>
                    <div style="font-size:11px;color:#6b7280;">Installs</div>
                </div>
                <div>
                    <div style="font-size:26px;font-weight:900;color:#059669;">${{ number_format($installStats['publisher_earnings'], 4) }}</div>
                    <div style="font-size:11px;color:#6b7280;">Earnings</div>
                </div>
            </div>
        </div>
        {{-- Actual (Admin Only) --}}
        <div style="background:#fff7ed;border:1.5px solid #fed7aa;border-radius:12px;padding:16px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#c2410c;margin-bottom:10px;">Actual (Admin Only — No Divider)</div>
            <div style="display:flex;gap:16px;">
                <div>
                    <div style="font-size:26px;font-weight:900;color:#ea580c;">{{ number_format($installStats['actual_installs']) }}</div>
                    <div style="font-size:11px;color:#6b7280;">Installs</div>
                </div>
                <div>
                    <div style="font-size:26px;font-weight:900;color:#ea580c;">${{ number_format($installStats['actual_earnings'], 4) }}</div>
                    <div style="font-size:11px;color:#6b7280;">Earnings</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Per-country breakdown --}}
    @if($installStats['publisher_by_country']->count() > 0 || $installStats['actual_by_country']->count() > 0)
    <div style="overflow-x:auto;">
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="text-align:left;padding:8px 10px;color:#6b7280;font-weight:600;">Country</th>
                    <th style="text-align:right;padding:8px 10px;color:#059669;font-weight:600;">Publisher Installs</th>
                    <th style="text-align:right;padding:8px 10px;color:#059669;font-weight:600;">Publisher Earnings</th>
                    <th style="text-align:right;padding:8px 10px;color:#c2410c;font-weight:600;">Actual Installs</th>
                    <th style="text-align:right;padding:8px 10px;color:#c2410c;font-weight:600;">Actual Earnings</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Merge both sets by country_code
                    $pubMap    = $installStats['publisher_by_country']->keyBy('country_code');
                    $actualMap = collect($installStats['actual_by_country'])->keyBy('country_code');
                    $allCodes  = $pubMap->keys()->merge($actualMap->keys())->unique();
                @endphp
                @foreach($allCodes as $code)
                @php
                    $pub    = $pubMap->get($code);
                    $actual = $actualMap->get($code);
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:8px 10px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="https://flagcdn.com/20x15/{{ strtolower($code) }}.png" style="border-radius:2px;" onerror="this.style.display='none'">
                            <span style="font-weight:600;">{{ $pub?->country_name ?? strtoupper($code) }}</span>
                            <code style="font-size:11px;color:#9ca3af;">{{ strtoupper($code) }}</code>
                        </div>
                    </td>
                    <td style="padding:8px 10px;text-align:right;font-weight:700;color:#059669;">{{ number_format($pub?->install_count ?? 0) }}</td>
                    <td style="padding:8px 10px;text-align:right;color:#059669;">${{ number_format($pub?->earnings ?? 0, 4) }}</td>
                    <td style="padding:8px 10px;text-align:right;font-weight:700;color:#ea580c;">{{ number_format($actual['installs'] ?? 0) }}</td>
                    <td style="padding:8px 10px;text-align:right;color:#ea580c;">${{ number_format($actual['earnings'] ?? 0, 4) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                    <td style="padding:10px;font-weight:700;">Total</td>
                    <td style="padding:10px;text-align:right;font-weight:700;color:#059669;">{{ number_format($installStats['publisher_installs']) }}</td>
                    <td style="padding:10px;text-align:right;font-weight:700;color:#059669;">${{ number_format($installStats['publisher_earnings'], 4) }}</td>
                    <td style="padding:10px;text-align:right;font-weight:700;color:#ea580c;">{{ number_format($installStats['actual_installs']) }}</td>
                    <td style="padding:10px;text-align:right;font-weight:700;color:#ea580c;">${{ number_format($installStats['actual_earnings'], 4) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:16px;color:#9ca3af;font-size:13px;">No installs recorded today yet.</div>
    @endif

    {{-- Retroactive recalculation action --}}
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:700;color:#374151;">Apply Divider Retroactively</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">
                Rebuilds all historical install records using the divider-adjusted threshold. Adjusts balance and earnings accordingly.
                Run this once to sync existing data with the new calculation.
            </div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.recalculate-installs', $user) }}"
              onsubmit="return confirm('Recalculate all install history for {{ addslashes($user->name) }} using divider {{ $installStats["divider_value"] }}×?\n\nThis will rebuild publisher_installs from Click data and adjust their balance. This cannot be undone.')">
            @csrf
            <button type="submit"
                    style="padding:9px 18px;background:#7c3aed;color:white;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Recalculate All History
            </button>
        </form>
    </div>
</div>
@endif

<!-- Click Chart -->
<div class="card mb-6">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
        <div style="width:36px;height:36px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:1px;">Click History</div>
            <div style="font-size:12px;color:#9ca3af;">Last 14 days — valid, Windows (actual), and fraud</div>
        </div>
        <a href="{{ route('admin.publishers.stats', $user) }}" style="margin-left:auto;font-size:12px;color:#3b82f6;font-weight:600;text-decoration:none;">
            View full stats →
        </a>
    </div>
    <div id="publisherClickChart"></div>
</div>

<!-- OS Breakdown Chart -->
@if($osBreakdown->isNotEmpty())
<div class="card mb-6">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
        <div style="width:36px;height:36px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:1px;">Clicks by OS</div>
            <div style="font-size:12px;color:#9ca3af;">Last 30 days — counted clicks only</div>
        </div>
        <div style="margin-left:auto;font-size:13px;font-weight:700;color:#374151;">
            {{ number_format($osBreakdown->sum()) }} total
        </div>
    </div>
    <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;">
        <div id="osDonutChart" style="flex-shrink:0;"></div>
        <div style="flex:1;min-width:200px;">
            @php
                $osTotal = $osBreakdown->sum();
                $osColors = ['#7c3aed','#01BF63','#3b82f6','#f59e0b','#ef4444','#06b6d4','#ec4899','#84cc16','#6b7280'];
                $i = 0;
            @endphp
            @foreach($osBreakdown as $os => $cnt)
            @php $pct = $osTotal > 0 ? round(($cnt / $osTotal) * 100, 1) : 0; @endphp
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <div style="width:10px;height:10px;border-radius:50%;background:{{ $osColors[$i % count($osColors)] }};flex-shrink:0;"></div>
                <div style="flex:1;font-size:13px;font-weight:600;color:#374151;">{{ $os }}</div>
                <div style="font-size:13px;color:#6b7280;">{{ number_format($cnt) }}</div>
                <div style="width:80px;">
                    <div style="height:5px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $osColors[$i % count($osColors)] }};border-radius:4px;"></div>
                    </div>
                </div>
                <div style="font-size:12px;color:#9ca3af;width:38px;text-align:right;">{{ $pct }}%</div>
            </div>
            @php $i++; @endphp
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Section: Management -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;margin-top:8px;">
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;white-space:nowrap;">Management</div>
    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
</div>

<div class="grid-3 mb-6">
    <!-- Click Divider -->
    <div class="card">
        <div class="card-title mb-1">Windows Click Divider</div>
        <div class="card-subtitle mb-4" style="font-size:12px;">Divides Windows clicks shown to publisher. Publisher will never know.</div>
        <form method="POST" action="{{ route('admin.publishers.update-divider', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Divider Value</label>
                <input type="number" name="divider_value" class="form-control" value="{{ $divider->divider_value }}" min="1" max="100" step="0.1">
            </div>
            <div class="toggle-wrap mb-4">
                <label class="toggle"><input type="checkbox" name="is_enabled" value="1" {{ $divider->is_enabled ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                <span style="font-size:13px;font-weight:500;">Enable divider</span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Divider</button>
        </form>
    </div>

    <!-- 48-Hour Test Results -->
    <div class="card">
        <div class="card-title mb-1">48-Hour Test Results</div>
        @php $testStatus = $user->publisherProfile?->test_status ?? 'not_started'; @endphp
        <div class="card-subtitle mb-4" style="font-size:12px;">
            Status:
            <strong style="color:{{ $testStatus === 'running' ? '#01BF63' : ($testStatus === 'completed' ? '#3b82f6' : '#6b7280') }};">
                {{ ucfirst(str_replace('_', ' ', $testStatus)) }}
            </strong>
        </div>

        @if($testStatus === 'not_started')
            <div style="background:#f9fafb;border-radius:8px;padding:12px;margin-bottom:12px;font-size:13px;color:#6b7280;">
                Waiting for <strong>20 unique clicks</strong> to auto-trigger the 48-hour test period.
            </div>
        @elseif($testStatus === 'running')
            @php
                $endAt = $user->publisherProfile->test_ended_at;
                $startAt = $user->publisherProfile->test_started_at;
                $hoursLeft = $endAt ? max(0, now()->diffInHours($endAt, false)) : 0;
                $minsLeft  = $endAt ? max(0, now()->diffInMinutes($endAt, false) % 60) : 0;
            @endphp
            <div style="background:#e6faf2;padding:12px;border-radius:8px;margin-bottom:12px;">
                <div style="font-size:18px;font-weight:800;color:#01BF63;">{{ $hoursLeft }}h {{ $minsLeft }}m remaining</div>
                <div style="font-size:11px;color:#6b7280;margin-top:4px;">
                    Started: {{ $startAt?->format('M d, Y H:i') ?? '—' }} &nbsp;·&nbsp;
                    Ends: {{ $endAt?->format('M d, Y H:i') ?? '—' }}
                </div>
            </div>
            @php $liveClicks = \App\Models\Click::where('user_id', $user->id)->where('is_counted', true)->count(); @endphp
            <div style="font-size:13px;color:#374151;margin-bottom:12px;">Current counted clicks: <strong>{{ number_format($liveClicks) }}</strong></div>
        @elseif($testStatus === 'completed')
            @php
                $startAt = $user->publisherProfile->test_started_at;
                $endAt   = $user->publisherProfile->test_ended_at;
                $duration = ($startAt && $endAt) ? $startAt->diffForHumans($endAt, true) : '—';
                $testClicks = \App\Models\Click::where('user_id', $user->id)->where('is_counted', true)->count();
            @endphp
            <div style="background:#dbeafe;padding:12px;border-radius:8px;margin-bottom:12px;">
                <div style="font-size:14px;font-weight:700;color:#1e40af;">Test Completed</div>
                <div style="font-size:12px;color:#1e40af;margin-top:4px;">Duration: {{ $duration }}</div>
                <div style="font-size:12px;color:#1e40af;">Total counted clicks: <strong>{{ number_format($testClicks) }}</strong></div>
            </div>
        @endif

        @if($user->publisherProfile?->test_total_clicks !== null)
            <div style="background:#f0fdf4;padding:12px;border-radius:8px;margin-bottom:12px;">
                <div style="font-size:20px;font-weight:800;color:#01BF63;">{{ number_format($user->publisherProfile->test_total_clicks) }}</div>
                <div style="font-size:12px;color:#6b7280;">Test Clicks (Manually Entered)</div>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.publishers.test-results', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Enter Total Clicks (manual override)</label>
                <input type="number" name="test_total_clicks" class="form-control" value="{{ $user->publisherProfile?->test_total_clicks ?? '' }}" placeholder="e.g. 5200" min="0">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Results</button>
        </form>
    </div>

    <!-- Offer Contract -->
    <div class="card">
        <div class="card-title mb-1">Offer Contract</div>
        <div class="card-subtitle mb-4" style="font-size:12px;">Publisher will accept or reject in their dashboard. Multiple offers can be pending simultaneously.</div>
        @php $pendingContracts = $user->contracts()->where('status','pending')->get(); @endphp
        @if($pendingContracts->isNotEmpty())
            <div style="margin-bottom:16px;">
                <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Pending Offers</div>
                @foreach($pendingContracts as $pc)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#fef3c7;border-radius:8px;margin-bottom:6px;gap:8px;">
                    <span style="font-size:13px;color:#92400e;font-weight:600;">
                        {{ ucfirst(str_replace('_', ' ', $pc->type)) }}
                        @if($pc->rate > 0) · ${{ $pc->rate }}@endif
                        @if($pc->admin_note) <span style="font-weight:400;font-size:11px;">({{ $pc->admin_note }})</span>@endif
                    </span>
                    <form method="POST" action="{{ route('admin.contracts.expire', $pc) }}" style="flex-shrink:0;">
                        @csrf
                        <button type="submit" style="padding:3px 10px;background:#fee2e2;color:#991b1b;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">Expire</button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.contracts.offer', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Contract Type</label>
                <select name="type" class="form-control form-select" id="contractType" onchange="toggleRate()">
                    <option value="per_click">Per 1,000 Unique Clicks</option>
                    <option value="fixed">Fixed Daily Rate</option>
                    <option value="installs_base">Installs Based</option>
                </select>
            </div>
            <div class="form-group" id="rateGroup" style="display:none;">
                <label class="form-label">Fixed Daily Rate (USD)</label>
                <input type="number" name="rate" id="rateInput" class="form-control" placeholder="0.0000" step="0.0001" min="0.0001">
            </div>
            <div id="perClickNote" style="background:#e6faf2;border-radius:8px;padding:12px;font-size:13px;color:#065f46;margin-bottom:12px;">
                ✓ Rate calculated automatically from country rates set in the Rates panel. No manual rate needed.
            </div>
            <div id="installsNote" style="display:none;background:#eff6ff;border-radius:8px;padding:12px;font-size:13px;color:#1e40af;margin-bottom:12px;">
                Publisher earns per install. Rates are configured in the <a href="{{ route('admin.install-rates.index') }}" style="color:#2563eb;font-weight:600;">Install Rates</a> page. No manual rate needed.
            </div>
            <div class="form-group">
                <label class="form-label">Note (optional)</label>
                <textarea name="admin_note" class="form-control" rows="2" placeholder="Internal note..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Send Contract Offer</button>
        </form>
        <script>
        function toggleRate() {
            const type = document.getElementById('contractType').value;
            document.getElementById('rateGroup').style.display = type === 'fixed' ? 'block' : 'none';
            document.getElementById('perClickNote').style.display = type === 'per_click' ? 'block' : 'none';
            document.getElementById('installsNote').style.display = type === 'installs_base' ? 'block' : 'none';
            document.getElementById('rateInput').required = type === 'fixed';
        }
        toggleRate();
        </script>
    </div>
</div>

<!-- Fixed Rate Adjustment (only for fixed-contract publishers) -->
@if($user->publisherProfile?->contract_type === 'fixed')
<div class="card mb-6" style="border:1.5px solid #bbf7d0;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <div style="width:38px;height:38px;background:#d1fae5;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <svg width="18" height="18" fill="none" stroke="#059669" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:2px;">Adjust Fixed Daily Rate</div>
            <div style="font-size:12px;color:#6b7280;">Publisher will be notified immediately when rate is changed.</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <div style="font-size:11px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Current Rate</div>
            <div style="font-size:24px;font-weight:900;color:#059669;">${{ number_format($user->publisherProfile->fixed_daily_rate, 4) }}<span style="font-size:13px;font-weight:600;color:#6b7280;">/day</span></div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.publishers.fixed-rate', $user) }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        @csrf
        <div style="flex:1;min-width:180px;">
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">New Daily Rate (USD)</label>
            <input type="number" name="fixed_daily_rate" class="form-control"
                   value="{{ $user->publisherProfile->fixed_daily_rate }}"
                   step="0.0001" min="0.0001" required
                   style="font-size:16px;font-weight:700;color:#059669;">
        </div>
        <button type="submit" class="btn btn-primary" onclick="return confirm('Update fixed daily rate for {{ addslashes($user->name) }}?\n\nThey will be notified immediately.')">
            Update Rate & Notify Publisher
        </button>
    </form>
</div>
@endif

<!-- Section: Publisher Settings -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;margin-top:8px;">
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;white-space:nowrap;">Publisher Settings</div>
    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
</div>

<!-- Payment Settings + Tags side by side -->
<div class="grid-2 mb-6">
    <!-- Payment Settings -->
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:34px;height:34px;background:#f0fdf4;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="card-title" style="margin-bottom:1px;">Payment Settings</div>
                <div style="font-size:12px;color:#9ca3af;">Control payment processing for this publisher</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
            <div style="background:#f0fdf4;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:18px;font-weight:800;color:#059669;">${{ number_format($user->publisherProfile?->balance ?? 0, 4) }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Current Balance</div>
            </div>
            <div style="background:#f9fafb;border-radius:10px;padding:12px;text-align:center;">
                <div style="font-size:18px;font-weight:800;color:#374151;">${{ number_format($user->publisherProfile?->total_earnings ?? 0, 4) }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px;">Total Earned</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.payment-status', $user) }}">
            @csrf
            <div class="toggle-wrap">
                <label class="toggle"><input type="checkbox" name="payment_enabled" value="1" {{ $user->publisherProfile?->payment_enabled ? 'checked' : '' }} onchange="this.form.submit()"><span class="toggle-slider"></span></label>
                <div>
                    <span style="font-size:13px;font-weight:600;">Enable Payments</span>
                    <div style="font-size:11px;color:#9ca3af;">Publisher can receive payouts when enabled</div>
                </div>
            </div>
        </form>
    </div>

    <!-- Publisher Tags -->
    <div class="card" style="border:1.5px solid #e5e7eb;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:34px;height:34px;background:#eff6ff;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div>
                <div class="card-title" style="margin-bottom:1px;">Publisher Tags</div>
                <div style="font-size:12px;color:#9ca3af;">Internal labels for categorizing this publisher</div>
            </div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:14px;min-height:32px;">
            @forelse($user->publisherTags as $tag)
            @php $tc = $tagColors[$tag->color] ?? '#6b7280'; @endphp
            <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $tc }}1a;color:{{ $tc }};border:1px solid {{ $tc }}40;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                {{ $tag->tag }}
                <form method="POST" action="{{ route('admin.publishers.tags.remove', $user) }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <input type="hidden" name="tag" value="{{ $tag->tag }}">
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:{{ $tc }};font-size:15px;line-height:1;padding:0;margin-left:1px;">×</button>
                </form>
            </span>
            @empty
            <span style="color:#9ca3af;font-size:13px;line-height:32px;">No tags yet</span>
            @endforelse
        </div>
        <form method="POST" action="{{ route('admin.publishers.tags.add', $user) }}" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div class="form-group" style="margin:0;flex:1;min-width:120px;">
                <label class="form-label">Tag Name</label>
                <input type="text" name="tag" class="form-control" placeholder="e.g. VIP, Trusted" maxlength="50" required>
            </div>
            <div class="form-group" style="margin:0;width:100px;">
                <label class="form-label">Color</label>
                <select name="color" class="form-control form-select">
                    <option value="green">Green</option>
                    <option value="blue">Blue</option>
                    <option value="amber">Amber</option>
                    <option value="red">Red</option>
                    <option value="purple">Purple</option>
                    <option value="gray">Gray</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">Add</button>
        </form>
    </div>
</div>

<!-- Section: Security -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;margin-top:8px;">
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;white-space:nowrap;">Security & Fraud</div>
    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
</div>

<!-- Domain Restriction + Fraud Detection side by side -->
<div class="grid-2 mb-6">
<!-- Domain Restriction card -->
<div class="card" style="border:1.5px solid #e5e7eb;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <div style="width:34px;height:34px;background:#fff7ed;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:1px;">Domain Restriction</div>
            <div style="font-size:12px;color:#9ca3af;">Control which referrer sources are accepted</div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.publishers.settings', $user) }}">
        @csrf
        <div class="toggle-wrap mb-4">
            <label class="toggle">
                <input type="hidden" name="enforce_domain_restriction" value="0">
                <input type="checkbox" name="enforce_domain_restriction" value="1" {{ $user->publisherProfile?->enforce_domain_restriction ?? true ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
            <div>
                <span style="font-size:13px;font-weight:600;">Enforce Domain Restriction</span>
                <div style="font-size:11px;color:#9ca3af;">Only count clicks from the publisher's registered domain. Disable to accept any referrer.</div>
            </div>
        </div>
        @if($user->website)
        <div style="background:#f9fafb;border-radius:8px;padding:10px 12px;margin-bottom:14px;font-size:12px;color:#6b7280;">
            Registered domain: <strong style="color:#374151;">{{ parse_url($user->website, PHP_URL_HOST) ?: $user->website }}</strong>
        </div>
        @endif
        <button type="submit" class="btn btn-primary btn-sm">Save Setting</button>
    </form>
</div>

<!-- Fraud Detection card -->
<div class="card" style="border:1.5px solid #e5e7eb;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <div style="width:34px;height:34px;background:#fef2f2;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#ef4444" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:1px;">Fraud Detection</div>
            <div style="font-size:12px;color:#9ca3af;">Per-publisher fraud checks — enable only what is needed</div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.publishers.fraud-settings', $user) }}">
        @csrf
        <div class="toggle-wrap mb-3">
            <label class="toggle"><input type="checkbox" name="fraud_headless_browser" value="1" {{ $user->publisherProfile?->fraud_headless_browser ? 'checked' : '' }}><span class="toggle-slider"></span></label>
            <div>
                <span style="font-size:13px;font-weight:600;">Headless Browser Detection</span>
                <div style="font-size:11px;color:#9ca3af;">Blocks Selenium, Puppeteer, PhantomJS</div>
            </div>
        </div>
        <div class="toggle-wrap mb-3">
            <label class="toggle"><input type="checkbox" name="fraud_country_mismatch" value="1" {{ $user->publisherProfile?->fraud_country_mismatch ? 'checked' : '' }}><span class="toggle-slider"></span></label>
            <div>
                <span style="font-size:13px;font-weight:600;">Country Mismatch Detection</span>
                <div style="font-size:11px;color:#9ca3af;">Flags IP country vs. browser language mismatch</div>
            </div>
        </div>
        <div class="toggle-wrap mb-4">
            <label class="toggle"><input type="checkbox" name="fraud_suspicious_referrer" value="1" {{ $user->publisherProfile?->fraud_suspicious_referrer ? 'checked' : '' }}><span class="toggle-slider"></span></label>
            <div>
                <span style="font-size:13px;font-weight:600;">Suspicious Referrer Detection</span>
                <div style="font-size:11px;color:#9ca3af;">Blocks known exchanges, PTC, and bot farms</div>
            </div>
        </div>
        <div class="form-group mb-4">
            <label class="form-label">Country Whitelist <span style="font-weight:400;color:#9ca3af;">(comma-separated, e.g. ID, PK, US)</span></label>
            <input type="text" name="allowed_countries" class="form-control"
                value="{{ $user->publisherProfile?->allowed_countries ? implode(', ', $user->publisherProfile->allowed_countries) : '' }}"
                placeholder="Leave empty to allow all countries">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Save Fraud Settings</button>
    </form>
</div>
</div>{{-- end grid-2 --}}

<!-- Section: Assets -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;margin-top:24px;">
    <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;white-space:nowrap;">Tracking Links & Websites</div>
    <div style="flex:1;height:1px;background:#e5e7eb;"></div>
</div>

<!-- Tracking Links -->
@php
    $maxClicks = $user->trackingLinks->max('unique_clicks') ?: 1;
@endphp
<div class="card mb-5">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
        <div style="width:36px;height:36px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:1px;">Tracking Links</div>
            <div style="font-size:12px;color:#9ca3af;">{{ $user->trackingLinks->count() }} link(s) assigned to this publisher</div>
        </div>
        <a href="{{ route('admin.tracking.create') }}" class="btn btn-primary btn-sm" style="margin-left:auto;">+ Add Link</a>
    </div>
    @forelse($user->trackingLinks as $link)
    @php $pct = $maxClicks > 0 ? round(($link->unique_clicks / $maxClicks) * 100) : 0; @endphp
    <div style="padding:14px 0;border-bottom:1px solid #f3f4f6;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <!-- Status dot -->
            <div style="width:8px;height:8px;border-radius:50%;background:{{ $link->is_active ? '#10b981' : '#ef4444' }};flex-shrink:0;margin-top:1px;"></div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:700;color:#111827;">{{ $link->name ?: 'Unnamed Link' }}</div>
                <div style="font-size:11px;color:#9ca3af;font-family:monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $link->tracking_url }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div style="font-size:16px;font-weight:800;color:#111827;">{{ number_format($link->unique_clicks) }}</div>
                <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;">valid clicks</div>
            </div>
            <div style="text-align:right;flex-shrink:0;min-width:52px;">
                <div style="font-size:14px;font-weight:700;color:#ef4444;">{{ number_format($link->fraud_clicks) }}</div>
                <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.04em;">fraud</div>
            </div>
            <span class="badge {{ $link->is_active ? 'badge-success' : 'badge-danger' }}" style="flex-shrink:0;">{{ $link->is_active ? 'Active' : 'Off' }}</span>
        </div>
        <!-- Click share bar -->
        <div style="margin-left:20px;">
            <div style="height:4px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#01BF63,#3b82f6);border-radius:4px;transition:width 0.4s;"></div>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:32px;color:#9ca3af;">
        <svg width="32" height="32" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        No tracking links assigned yet
    </div>
    @endforelse
</div>

<!-- Publisher Websites -->
{{-- $publisherWebsites loaded defensively in controller --}}
<div class="card">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
        <div style="width:36px;height:36px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        </div>
        <div>
            <div class="card-title" style="margin-bottom:1px;">Submitted Websites</div>
            <div style="font-size:12px;color:#9ca3af;">Websites registered by this publisher for additional ad codes</div>
        </div>
        <a href="{{ route('admin.publisher-websites.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">View All →</a>
    </div>
    @forelse($publisherWebsites as $website)
    <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f3f4f6;flex-wrap:wrap;">
        <div style="width:36px;height:36px;background:#f9fafb;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        </div>
        <div style="flex:1;min-width:160px;">
            <div style="font-size:13px;font-weight:700;color:#111827;">{{ $website->domain }}</div>
            <div style="font-size:11px;color:#9ca3af;">Submitted {{ $website->created_at->format('M d, Y') }}</div>
        </div>
        <span class="badge {{ $website->status === 'approved' ? 'badge-success' : ($website->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
            {{ ucfirst($website->status) }}
        </span>
        @if($website->trackingLink)
        <div style="text-align:right;">
            <div style="font-size:13px;font-weight:700;">{{ number_format($website->trackingLink->unique_clicks) }}</div>
            <div style="font-size:11px;color:#9ca3af;">valid clicks</div>
        </div>
        @endif
        @if($website->isPending())
        <a href="{{ route('admin.publisher-websites.index') }}" class="btn btn-primary btn-sm">Review</a>
        @endif
    </div>
    @empty
    <div style="text-align:center;padding:32px;color:#9ca3af;">
        <svg width="32" height="32" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        No website submissions yet
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
// OS Donut Chart
@if($osBreakdown->isNotEmpty())
new ApexCharts(document.getElementById('osDonutChart'), {
    series: @json($osBreakdown->values()),
    labels: @json($osBreakdown->keys()),
    chart: { type: 'donut', width: 220, height: 220 },
    colors: ['#7c3aed','#01BF63','#3b82f6','#f59e0b','#ef4444','#06b6d4','#ec4899','#84cc16','#6b7280'],
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', color: '#6b7280', formatter: (w) => w.globals.seriesTotals.reduce((a,b)=>a+b,0).toLocaleString() } } } } },
    stroke: { width: 2 },
    tooltip: { y: { formatter: (v) => v.toLocaleString() + ' clicks' } }
}).render();
@endif

const chartData = @json($clicksChart);
new ApexCharts(document.getElementById('publisherClickChart'), {
    series: [
        { name: 'Valid Clicks (Shown to Publisher)', data: chartData.map(d => d.actual) },
        { name: 'Windows (Actual - Admin Only)', data: chartData.map(d => d.windows) },
        { name: 'Fraud', data: chartData.map(d => d.fraud) }
    ],
    chart: { type: 'bar', height: 250, toolbar: { show: false }, stacked: false },
    colors: ['#01BF63', '#f59e0b', '#ef4444'],
    xaxis: { categories: chartData.map(d => d.date), labels: { style: { fontSize: '11px' } } },
    legend: { position: 'top' },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' }
}).render();
</script>
@endpush
