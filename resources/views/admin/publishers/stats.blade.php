@extends('layouts.admin')
@section('title', $user->name . ' — Stats')
@section('page-title', $user->name . ' — Detailed Stats')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;flex-wrap:wrap;">
    <a href="{{ route('admin.publishers.show', $user) }}" class="btn btn-ghost btn-sm">← Back to Publisher</a>
    <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($user->status) }}</span>
    <span style="font-size:13px;color:#9ca3af;">{{ $user->email }}</span>
</div>

{{-- Period Filter + Export --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
    @foreach(['1' => 'Today', '7' => '7 Days', '30' => '30 Days', '90' => '90 Days', 'all' => 'All Time'] as $val => $label)
        <a href="?period={{ $val }}{{ $selectedLink ? '&link_id='.$selectedLink->id : '' }}"
           class="btn btn-sm {{ $period == $val ? 'btn-primary' : 'btn-ghost' }}">{{ $label }}</a>
    @endforeach
    <a href="{{ route('admin.publishers.stats.export', [$user, 'period' => $period]) }}"
       class="btn btn-sm btn-ghost" style="margin-left:auto;color:#3b82f6;border-color:#3b82f6;">
        ↓ Export CSV
    </a>
</div>

{{-- Tracking Link Filter --}}
@if($allLinks->isNotEmpty())
<div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;padding:12px 14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;">
    <span style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;flex-shrink:0;">Filter by Link:</span>
    <a href="?period={{ $period }}"
       class="btn btn-sm {{ !$selectedLink ? 'btn-primary' : 'btn-ghost' }}"
       style="font-size:12px;">All Links</a>
    @foreach($allLinks as $link)
    <a href="?period={{ $period }}&link_id={{ $link->id }}"
       class="btn btn-sm {{ $selectedLink?->id === $link->id ? 'btn-primary' : 'btn-ghost' }}"
       style="font-size:12px;">
        {{ $link->name ?: $link->unique_code }}
        <span style="font-size:10px;opacity:0.6;margin-left:3px;">{{ $link->unique_code }}</span>
    </a>
    @endforeach
</div>
@endif

{{-- Active filter badge --}}
@if($selectedLink)
<div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;padding:10px 14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;">
    <svg width="14" height="14" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
    <span style="font-size:13px;color:#1e40af;font-weight:600;">Showing stats for: <strong>{{ $selectedLink->name ?: $selectedLink->unique_code }}</strong></span>
    <code style="font-size:11px;color:#3b82f6;background:#dbeafe;padding:1px 6px;border-radius:4px;">{{ $selectedLink->unique_code }}</code>
    <a href="?period={{ $period }}" style="margin-left:auto;font-size:12px;color:#6b7280;text-decoration:none;">✕ Clear filter</a>
</div>
@endif

{{-- Summary Cards --}}
<div class="stats-grid mb-6" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr));">
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3f4f6;">
            <svg width="20" height="20" fill="none" stroke="#6b7280" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
        </div>
        <div class="stat-value">{{ number_format($summary['total_raw']) }}</div>
        <div class="stat-label">Total Raw Clicks</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">
            <svg width="20" height="20" fill="none" stroke="#059652" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-value" style="color:#01BF63;">{{ number_format($summary['valid']) }}</div>
        <div class="stat-label">Valid Clicks</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;">
            <svg width="20" height="20" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-value" style="color:#ef4444;">{{ number_format($summary['fraud']) }}</div>
        <div class="stat-label">Fraud Clicks</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-value" style="color:#3b82f6;">{{ number_format($summary['windows']) }}</div>
        <div class="stat-label">Windows Clicks</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;">
            <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="stat-value" style="color:#f59e0b;">{{ $summary['fraud_rate'] }}%</div>
        <div class="stat-label">Fraud Rate</div>
    </div>
    @if($installsData)
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede9fe;">
            <svg width="20" height="20" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div class="stat-value" style="color:#7c3aed;">{{ number_format($installsData['total_installs']) }}</div>
        <div class="stat-label">Win Installs</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">
            <svg width="20" height="20" fill="none" stroke="#059652" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        </div>
        <div class="stat-value" style="color:#01BF63;">${{ number_format($installsData['total_earnings'], 4) }}</div>
        <div class="stat-label">Win Install Earnings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;">
            <svg width="20" height="20" fill="none" stroke="#8b5cf6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </div>
        <div class="stat-value" style="color:#8b5cf6;">{{ number_format($installsData['mac_total_installs']) }}</div>
        <div class="stat-label">Mac Installs</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;">
            <svg width="20" height="20" fill="none" stroke="#8b5cf6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        </div>
        <div class="stat-value" style="color:#8b5cf6;">${{ number_format($installsData['mac_total_earnings'], 4) }}</div>
        <div class="stat-label">Mac Install Earnings</div>
    </div>
    @else
    <div class="stat-card">
        <div class="stat-icon" style="background:#d1fae5;">
            <svg width="20" height="20" fill="none" stroke="#059652" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
        </div>
        <div class="stat-value" style="color:#01BF63;">${{ number_format($summary['earnings'], 4) }}</div>
        <div class="stat-label">Total Earnings</div>
    </div>
    @endif
</div>

{{-- Daily Chart --}}
<div class="card mb-6">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div class="card-title">Day-by-Day Breakdown</div>
        <span style="font-size:12px;color:#9ca3af;">{{ count($daily) }} days</span>
    </div>
    <div id="dailyChart"></div>

    {{-- Day-by-day table --}}
    @if(count($daily) > 0)
    <div style="margin-top:20px;overflow-x:auto;">
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="text-align:left;padding:8px 12px;color:#6b7280;font-weight:600;">Date</th>
                    <th style="text-align:right;padding:8px 12px;color:#6b7280;font-weight:600;">Valid Clicks</th>
                    <th style="text-align:right;padding:8px 12px;color:#6b7280;font-weight:600;">Fraud Clicks</th>
                    <th style="text-align:right;padding:8px 12px;color:#6b7280;font-weight:600;">Windows</th>
                    @if($installsData)
                    <th style="text-align:right;padding:8px 12px;color:#7c3aed;font-weight:600;">Win Installs</th>
                    <th style="text-align:right;padding:8px 12px;color:#6b7280;font-weight:600;">Win Earnings</th>
                    <th style="text-align:right;padding:8px 12px;color:#8b5cf6;font-weight:600;">Mac Installs</th>
                    <th style="text-align:right;padding:8px 12px;color:#8b5cf6;font-weight:600;">Mac Earnings</th>
                    @else
                    <th style="text-align:right;padding:8px 12px;color:#6b7280;font-weight:600;">Earnings (USD)</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php $totalEarnings = 0; @endphp
                @foreach($daily as $row)
                @php $totalEarnings += $installsData ? ($row['install_earnings'] ?? 0) : $row['earnings']; @endphp
                <tr style="border-bottom:1px solid #f3f4f6;{{ $row['fraud'] > 0 ? 'background:#fffbf5;' : '' }}">
                    <td style="padding:8px 12px;font-weight:600;color:#374151;">{{ $row['date'] }}</td>
                    <td style="padding:8px 12px;text-align:right;color:#01BF63;font-weight:700;">{{ number_format($row['valid']) }}</td>
                    <td style="padding:8px 12px;text-align:right;color:{{ $row['fraud'] > 0 ? '#ef4444' : '#9ca3af' }};font-weight:{{ $row['fraud'] > 0 ? '700' : '400' }};">{{ number_format($row['fraud']) }}</td>
                    <td style="padding:8px 12px;text-align:right;color:#3b82f6;">{{ number_format($row['windows']) }}</td>
                    @if($installsData)
                    <td style="padding:8px 12px;text-align:right;color:#7c3aed;font-weight:700;">{{ number_format($row['installs'] ?? 0) }}</td>
                    <td style="padding:8px 12px;text-align:right;color:#374151;font-family:monospace;">${{ number_format($row['install_earnings'] ?? 0, 4) }}</td>
                    <td style="padding:8px 12px;text-align:right;color:#8b5cf6;font-weight:700;">{{ number_format($row['mac_installs'] ?? 0) }}</td>
                    <td style="padding:8px 12px;text-align:right;color:#8b5cf6;font-family:monospace;">${{ number_format($row['mac_install_earnings'] ?? 0, 4) }}</td>
                    @else
                    <td style="padding:8px 12px;text-align:right;color:#374151;font-family:monospace;">${{ number_format($row['earnings'], 4) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                    <td style="padding:10px 12px;font-weight:700;">Total</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;color:#01BF63;">{{ number_format(array_sum(array_column($daily, 'valid'))) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;color:#ef4444;">{{ number_format(array_sum(array_column($daily, 'fraud'))) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;color:#3b82f6;">{{ number_format(array_sum(array_column($daily, 'windows'))) }}</td>
                    @if($installsData)
                    <td style="padding:10px 12px;text-align:right;font-weight:700;color:#7c3aed;">{{ number_format($installsData['total_installs']) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;font-family:monospace;">${{ number_format($installsData['total_earnings'], 4) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;color:#8b5cf6;">{{ number_format($installsData['mac_total_installs']) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;font-family:monospace;color:#8b5cf6;">${{ number_format($installsData['mac_total_earnings'], 4) }}</td>
                    @else
                    <td style="padding:10px 12px;text-align:right;font-weight:700;font-family:monospace;">${{ number_format($totalEarnings, 4) }}</td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

{{-- Windows Installs by Country (installs_base only) --}}
@if($installsData)
<div class="card mb-6">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div class="card-title">Windows Install Earnings by Country</div>
        <span style="background:#ede9fe;color:#7c3aed;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">Windows Installs</span>
    </div>
    @if($installsData['by_country']->isEmpty())
        <div style="text-align:center;padding:24px;color:#9ca3af;">No Windows install data for this period</div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Country</th>
                    <th style="text-align:right;">Installs</th>
                    <th style="text-align:right;">Earnings (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installsData['by_country'] as $inst)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="https://flagcdn.com/24x18/{{ strtolower($inst['country_code']) }}.png"
                                 style="width:24px;height:18px;border-radius:3px;object-fit:cover;flex-shrink:0;"
                                 onerror="this.style.display='none'">
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $inst['country_name'] }}</div>
                                <div style="font-size:11px;color:#9ca3af;"><code>{{ $inst['country_code'] }}</code></div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right;font-weight:700;color:#7c3aed;">{{ number_format($inst['install_count']) }}</td>
                    <td style="text-align:right;font-weight:600;color:#01BF63;">${{ number_format($inst['earnings'], 4) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                    <td style="padding:10px 12px;font-weight:700;">Total</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;color:#7c3aed;">{{ number_format($installsData['total_installs']) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;font-family:monospace;color:#01BF63;">${{ number_format($installsData['total_earnings'], 4) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>

{{-- Mac Installs by Country --}}
<div class="card mb-6" style="border:1.5px solid #c4b5fd;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div class="card-title">Mac Install Earnings by Country</div>
        <span style="background:#f5f3ff;color:#8b5cf6;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">Mac Installs</span>
    </div>
    @if($installsData['mac_by_country']->isEmpty())
        <div style="text-align:center;padding:24px;color:#9ca3af;">No Mac install data for this period</div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Country</th>
                    <th style="text-align:right;">Mac Installs</th>
                    <th style="text-align:right;">Earnings (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installsData['mac_by_country'] as $inst)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="https://flagcdn.com/24x18/{{ strtolower($inst['country_code']) }}.png"
                                 style="width:24px;height:18px;border-radius:3px;object-fit:cover;flex-shrink:0;"
                                 onerror="this.style.display='none'">
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $inst['country_name'] }}</div>
                                <div style="font-size:11px;color:#9ca3af;"><code>{{ $inst['country_code'] }}</code></div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right;font-weight:700;color:#8b5cf6;">{{ number_format($inst['install_count']) }}</td>
                    <td style="text-align:right;font-weight:600;color:#8b5cf6;">${{ number_format($inst['earnings'], 4) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #e5e7eb;background:#f9fafb;">
                    <td style="padding:10px 12px;font-weight:700;">Total</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;color:#8b5cf6;">{{ number_format($installsData['mac_total_installs']) }}</td>
                    <td style="padding:10px 12px;text-align:right;font-weight:700;font-family:monospace;color:#8b5cf6;">${{ number_format($installsData['mac_total_earnings'], 4) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
@endif

<div class="grid-2 mb-6" style="align-items:start;">

    {{-- Fraud by Type --}}
    <div class="card">
        <div class="card-title mb-4">Fraud Breakdown by Type</div>
        @if(empty($fraudByType))
            <div style="text-align:center;padding:24px;color:#9ca3af;">No fraud detected in this period</div>
        @else
            @php
                $fraudLabels = [
                    'duplicate_ip'  => ['label' => 'Duplicate IP', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
                    'vpn_detected'  => ['label' => 'VPN Detected', 'color' => '#ef4444', 'bg' => '#fee2e2'],
                    'proxy_detected'=> ['label' => 'Proxy Detected','color' => '#dc2626','bg' => '#fee2e2'],
                    'bot_detected'  => ['label' => 'Bot / Script',  'color' => '#7c3aed', 'bg' => '#ede9fe'],
                    'traffic_spike' => ['label' => 'Traffic Spike', 'color' => '#0891b2', 'bg' => '#cffafe'],
                ];
                $totalFraud = array_sum($fraudByType);
            @endphp
            <div id="fraudPieChart" style="margin-bottom:16px;"></div>
            @foreach($fraudByType as $type => $count)
                @php $meta = $fraudLabels[$type] ?? ['label' => ucwords(str_replace('_',' ',$type)), 'color' => '#6b7280', 'bg' => '#f3f4f6']; @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:{{ $meta['bg'] }};border-radius:8px;margin-bottom:6px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="width:10px;height:10px;background:{{ $meta['color'] }};border-radius:50%;display:inline-block;"></span>
                        <span style="font-size:13px;font-weight:600;color:#374151;">{{ $meta['label'] }}</span>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:15px;font-weight:700;color:{{ $meta['color'] }};">{{ number_format($count) }}</span>
                        <span style="font-size:12px;color:#9ca3af;margin-left:4px;">{{ $totalFraud > 0 ? round($count/$totalFraud*100,1) : 0 }}%</span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Device & OS --}}
    <div class="card">
        <div class="card-title mb-4">Device & OS Breakdown</div>

        <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:8px;">Device Type</div>
        @foreach($deviceTypes as $d)
        @php $pct = $summary['total_raw'] > 0 ? round($d->count/$summary['total_raw']*100,1) : 0; @endphp
        <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:3px;">
                <span style="font-weight:600;text-transform:capitalize;">{{ $d->device_type ?: 'Unknown' }}</span>
                <span style="color:#6b7280;">{{ number_format($d->count) }} ({{ $pct }}%)</span>
            </div>
            <div style="height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                <div style="width:{{ $pct }}%;height:100%;background:#01BF63;border-radius:3px;"></div>
            </div>
        </div>
        @endforeach

        <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-bottom:8px;margin-top:20px;">Operating System</div>
        @foreach($osByType as $o)
        @php $pct = $summary['total_raw'] > 0 ? round($o->count/$summary['total_raw']*100,1) : 0; @endphp
        <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:3px;">
                <span style="font-weight:600;">{{ $o->os ?: 'Unknown' }}</span>
                <span style="color:#6b7280;">{{ number_format($o->count) }} ({{ $pct }}%)</span>
            </div>
            <div style="height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden;">
                <div style="width:{{ $pct }}%;height:100%;background:#3b82f6;border-radius:3px;"></div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- Country Breakdown --}}
<div class="card mb-6">
    <div class="card-title mb-4">Traffic by Country</div>
    @if($topCountries->isEmpty())
        <div style="text-align:center;padding:24px;color:#9ca3af;">No data for this period</div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Valid Clicks</th>
                    <th>Fraud Clicks</th>
                    <th>Fraud Rate</th>
                    <th>Earnings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topCountries as $c)
                @php
                    $total = $c->valid_count + $c->fraud_count;
                    $fr = $total > 0 ? round($c->fraud_count / $total * 100, 1) : 0;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $c->country_name ?: $c->country_code }}</div>
                        <div style="font-size:11px;color:#9ca3af;"><code>{{ $c->country_code }}</code></div>
                    </td>
                    <td><span style="font-weight:700;color:#01BF63;">{{ number_format($c->valid_count) }}</span></td>
                    <td><span style="font-weight:700;color:#ef4444;">{{ number_format($c->fraud_count) }}</span></td>
                    <td>
                        <span style="background:{{ $fr > 30 ? '#fee2e2' : ($fr > 10 ? '#fef3c7' : '#f3f4f6') }};
                            color:{{ $fr > 30 ? '#dc2626' : ($fr > 10 ? '#d97706' : '#6b7280') }};
                            padding:2px 8px;border-radius:10px;font-size:12px;font-weight:600;">
                            {{ $fr }}%
                        </span>
                    </td>
                    <td style="color:#01BF63;font-weight:600;">${{ number_format($c->earnings, 4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Recent Click Log --}}
<div class="card">
    <div class="card-title mb-1">Recent Click Log</div>
    <div style="font-size:13px;color:#9ca3af;margin-bottom:16px;">Last 30 clicks</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>IP</th>
                    <th>Country</th>
                    <th>OS / Device</th>
                    <th>Status</th>
                    <th>Fraud Reason</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentClicks as $click)
                <tr style="{{ $click->is_fraud ? 'background:#fff5f5;' : '' }}">
                    <td style="font-size:12px;color:#9ca3af;white-space:nowrap;">{{ $click->created_at->format('M d H:i:s') }}</td>
                    <td><code style="font-size:11px;">{{ $click->ip_address }}</code></td>
                    <td style="font-size:13px;">{{ $click->country_code }}</td>
                    <td style="font-size:12px;">{{ $click->os }} / {{ $click->device_type }}</td>
                    <td>
                        @if($click->is_fraud)
                            <span class="badge badge-danger">Fraud</span>
                        @elseif($click->is_counted)
                            <span class="badge badge-success">Valid</span>
                        @else
                            <span class="badge badge-gray">Skipped</span>
                        @endif
                        @if($click->is_vpn)<span class="badge badge-danger" style="margin-left:2px;">VPN</span>@endif
                        @if($click->is_proxy)<span class="badge badge-danger" style="margin-left:2px;">Proxy</span>@endif
                    </td>
                    <td style="font-size:12px;color:#9ca3af;">
                        {{ $click->fraud_reason ? ucwords(str_replace('_', ' ', $click->fraud_reason)) : '—' }}
                    </td>
                    <td style="font-weight:600;color:{{ $click->click_value > 0 ? '#01BF63' : '#9ca3af' }};">
                        {{ $click->click_value > 0 ? '$'.number_format($click->click_value, 6) : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:24px;color:#9ca3af;">No clicks in this period</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
const daily = @json($daily);

@php $isInstallsBase = $installsData !== null; @endphp
const isInstallsBase = @json($isInstallsBase);
new ApexCharts(document.getElementById('dailyChart'), {
    series: isInstallsBase ? [
        { name: 'Valid Clicks', data: daily.map(d => d.valid) },
        { name: 'Windows Clicks', data: daily.map(d => d.windows) },
        { name: 'Fraud Clicks', data: daily.map(d => d.fraud) },
        { name: 'Installs', data: daily.map(d => d.installs || 0) },
    ] : [
        { name: 'Valid Clicks', data: daily.map(d => d.valid) },
        { name: 'Windows Clicks', data: daily.map(d => d.windows) },
        { name: 'Fraud Clicks', data: daily.map(d => d.fraud) },
    ],
    chart: { type: 'bar', height: 240, toolbar: { show: false }, stacked: false },
    colors: isInstallsBase ? ['#01BF63', '#3b82f6', '#ef4444', '#7c3aed'] : ['#01BF63', '#3b82f6', '#ef4444'],
    xaxis: { categories: daily.map(d => d.date), labels: { style: { fontSize: '11px' } } },
    legend: { position: 'top' },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' },
    plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
    tooltip: { y: { formatter: val => val.toLocaleString() } },
}).render();

@if(!empty($fraudByType))
@php
    $fraudChartData   = array_values($fraudByType);
    $fraudChartLabels = array_map(function($t) { return ucwords(str_replace('_', ' ', $t)); }, array_keys($fraudByType));
@endphp
const fraudData = @json($fraudChartData);
const fraudLabels = @json($fraudChartLabels);
new ApexCharts(document.getElementById('fraudPieChart'), {
    series: fraudData,
    labels: fraudLabels,
    chart: { type: 'donut', height: 200 },
    colors: ['#f59e0b', '#ef4444', '#dc2626', '#7c3aed', '#0891b2'],
    legend: { position: 'bottom', fontSize: '12px' },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '60%' } } },
}).render();
@endif
</script>
@endpush
