@extends('layouts.admin')

@section('title', 'Campaign: ' . $campaign->name)

@section('content')
@php
    $statusColors = [
        'draft'            => '#6b7280',
        'pending_approval' => '#8b5cf6',
        'pending_payment'  => '#f59e0b',
        'active'           => '#10b981',
        'paused'           => '#3b82f6',
        'completed'        => '#8b5cf6',
        'cancelled'        => '#ef4444',
    ];
    $sc = $statusColors[$campaign->status] ?? '#6b7280';
@endphp

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;flex-wrap:wrap;">
            <h1 class="page-title" style="margin:0;">{{ $campaign->name }}</h1>
            <span style="background:{{ $sc }}20;color:{{ $sc }};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;border:1px solid {{ $sc }}40;">
                {{ ucfirst(str_replace('_',' ',$campaign->status)) }}
            </span>
        </div>
        <p style="color:var(--text-muted);font-size:13px;margin:0;">
            Advertiser: <a href="{{ route('admin.advertisers.show', $campaign->user) }}" style="color:var(--primary);font-weight:600;">{{ $campaign->user->name }}</a>
            &nbsp;·&nbsp;
            <a href="{{ route('admin.campaigns.index') }}" style="color:var(--text-muted);">← All Campaigns</a>
        </p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        @if($campaign->status === 'draft' && $campaign->total_value > 0)
            <form method="POST" action="{{ route('admin.campaigns.send-contract', $campaign) }}">
                @csrf
                <button class="btn" style="background:#8b5cf6;color:white;font-weight:700;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Send Contract to Advertiser
                </button>
            </form>
        @elseif($campaign->status === 'pending_approval')
            <span style="background:#f5f3ff;color:#7c3aed;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;border:1px solid #ede9fe;">
                ⏳ Awaiting Advertiser Approval
            </span>
        @endif
        @if($campaign->status === 'active')
            <form method="POST" action="{{ route('admin.campaigns.pause', $campaign) }}">
                @csrf <button class="btn btn-secondary">Pause</button>
            </form>
        @elseif($campaign->status === 'paused')
            <form method="POST" action="{{ route('admin.campaigns.resume', $campaign) }}">
                @csrf <button class="btn btn-primary">Resume</button>
            </form>
        @endif
        @if(!in_array($campaign->status, ['completed','cancelled']))
            <form method="POST" action="{{ route('admin.campaigns.cancel', $campaign) }}" onsubmit="return confirm('Cancel this campaign?')">
                @csrf <button class="btn" style="background:#ef4444;color:#fff;">Cancel</button>
            </form>
        @endif
    </div>
</div>

{{-- Stats row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:24px;">
    <div class="card" style="padding:16px;text-align:center;border-top:3px solid #3b82f6;">
        <div style="font-size:24px;font-weight:800;color:#3b82f6;">{{ number_format($campaign->delivered_clicks) }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Delivered</div>
    </div>
    <div class="card" style="padding:16px;text-align:center;border-top:3px solid #e5e7eb;">
        <div style="font-size:24px;font-weight:800;">{{ number_format($campaign->target_clicks) }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Target</div>
    </div>
    <div class="card" style="padding:16px;text-align:center;border-top:3px solid #f59e0b;">
        <div style="font-size:24px;font-weight:800;color:#d97706;">${{ number_format($campaign->total_value,2) }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Total Value</div>
    </div>
    <div class="card" style="padding:16px;text-align:center;border-top:3px solid #10b981;">
        <div style="font-size:24px;font-weight:800;color:#059669;">${{ number_format($campaign->total_paid,2) }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Paid</div>
    </div>
    <div class="card" style="padding:16px;text-align:center;border-top:3px solid {{ $sc }};">
        <div style="font-size:18px;font-weight:700;color:{{ $sc }};">{{ ucfirst(str_replace('_',' ',$campaign->status)) }}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Status</div>
    </div>
    <div class="card" style="padding:16px;text-align:center;border-top:3px solid #8b5cf6;">
        <div style="font-size:24px;font-weight:800;color:#7c3aed;">{{ $campaign->progressPercent() }}%</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Progress</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Campaign Details --}}
    <div class="card" style="padding:20px;">
        <h3 style="margin:0 0 16px;font-size:15px;font-weight:700;">Campaign Details</h3>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <tr><td style="padding:6px 0;color:var(--text-muted);width:45%;vertical-align:top;">Destination URL</td>
                <td style="padding:6px 0;word-break:break-all;"><a href="{{ $campaign->destination_url }}" target="_blank" style="color:var(--primary);">{{ Str::limit($campaign->destination_url,50) }}</a></td></tr>
            @if($campaign->fallback_url)
            <tr><td style="padding:6px 0;color:var(--text-muted);">Fallback URL</td>
                <td style="padding:6px 0;word-break:break-all;"><a href="{{ $campaign->fallback_url }}" target="_blank" style="color:#9ca3af;">{{ Str::limit($campaign->fallback_url,50) }}</a></td></tr>
            @endif
            <tr><td style="padding:6px 0;color:var(--text-muted);">Contract Type</td>
                <td style="padding:6px 0;font-weight:600;">{{ $campaign->contract_type === 'fixed_rate' ? 'Fixed Rate' : 'Per-Click by Country' }}</td></tr>
            @if($campaign->contract_type === 'fixed_rate' && $campaign->fixed_rate)
            <tr><td style="padding:6px 0;color:var(--text-muted);">Fixed Rate</td>
                <td style="padding:6px 0;font-weight:600;">${{ $campaign->fixed_rate }} / click</td></tr>
            @endif
            <tr><td style="padding:6px 0;color:var(--text-muted);">Advance Required (50%)</td>
                <td style="padding:6px 0;font-weight:600;color:#f59e0b;">${{ number_format($campaign->advance_amount,2) }}</td></tr>
            <tr><td style="padding:6px 0;color:var(--text-muted);">Started</td>
                <td style="padding:6px 0;">{{ $campaign->started_at?->format('M d, Y H:i') ?? '—' }}</td></tr>
            <tr><td style="padding:6px 0;color:var(--text-muted);">Completed</td>
                <td style="padding:6px 0;">{{ $campaign->completed_at?->format('M d, Y H:i') ?? '—' }}</td></tr>
            @if($campaign->admin_note)
            <tr><td style="padding:6px 0;color:var(--text-muted);vertical-align:top;">Admin Note</td>
                <td style="padding:6px 0;white-space:pre-wrap;">{{ $campaign->admin_note }}</td></tr>
            @endif
        </table>
        @if($campaign->target_clicks)
        <div style="margin-top:16px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-bottom:5px;">
                <span>{{ number_format($campaign->delivered_clicks) }} / {{ number_format($campaign->target_clicks) }}</span>
                <span>{{ $campaign->progressPercent() }}%</span>
            </div>
            <div style="background:#e5e7eb;border-radius:999px;height:8px;overflow:hidden;">
                <div style="height:100%;border-radius:999px;background:var(--primary);width:{{ $campaign->progressPercent() }}%;"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Set Rates Form --}}
    @if(in_array($campaign->status, ['draft','pending_approval','pending_payment','active','paused']))
    <div class="card" style="padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
            <h3 style="margin:0;font-size:15px;font-weight:700;">Set / Update Rates</h3>
            @if($campaign->status === 'draft')
            <button type="button" onclick="loadDefaultRates()" style="padding:6px 12px;background:#eff6ff;color:#3b82f6;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                ↓ Load Default Rates
            </button>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.campaigns.rates', $campaign) }}" id="ratesForm">
            @csrf
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:6px;">Contract Type</label>
                <div style="display:flex;gap:16px;">
                    <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer;">
                        <input type="radio" name="contract_type" value="fixed_rate" {{ $campaign->contract_type === 'fixed_rate' ? 'checked' : '' }} onchange="toggleRateFields(this.value)"> Fixed Rate
                    </label>
                    <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer;">
                        <input type="radio" name="contract_type" value="per_click" {{ $campaign->contract_type === 'per_click' ? 'checked' : '' }} onchange="toggleRateFields(this.value)"> Per-Click by Country
                    </label>
                </div>
            </div>

            <div id="fixed-rate-field" style="margin-bottom:12px;{{ $campaign->contract_type !== 'fixed_rate' ? 'display:none' : '' }}">
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Fixed Rate ($/click)</label>
                <input type="number" name="fixed_rate" step="0.000001" min="0" value="{{ $campaign->fixed_rate }}"
                       style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;">
            </div>

            <div id="country-rates-field" style="margin-bottom:12px;{{ $campaign->contract_type !== 'per_click' ? 'display:none' : '' }}">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <label style="font-size:12px;font-weight:600;">Country Rates</label>
                    <a href="{{ route('admin.advertiser-rates.index') }}" target="_blank" style="font-size:11px;color:#3b82f6;">Manage master rates →</a>
                </div>
                <div id="countryRatesTable" style="max-height:220px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;">
                    @php $existingRates = $campaign->country_rates ?? []; @endphp
                    @foreach($masterRates as $mr)
                    <div style="display:flex;align-items:center;gap:8px;padding:7px 10px;border-bottom:1px solid #f3f4f6;">
                        <img src="https://flagcdn.com/16x12/{{ strtolower($mr->country_code) }}.png" width="16" height="12" style="border-radius:2px;">
                        <span style="font-size:12px;flex:1;">{{ $mr->country_name }}</span>
                        <code style="font-size:11px;color:#6b7280;">{{ strtoupper($mr->country_code) }}</code>
                        <span style="font-size:11px;color:#9ca3af;">$</span>
                        <input type="number" name="cr[{{ $mr->country_code }}]"
                               value="{{ $existingRates[$mr->country_code] ?? $mr->rate_usd }}"
                               step="0.000001" min="0"
                               style="width:80px;padding:3px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:12px;text-align:right;">
                    </div>
                    @endforeach
                    @if($masterRates->isEmpty())
                    <div style="padding:16px;text-align:center;font-size:13px;color:#9ca3af;">
                        No master rates set. <a href="{{ route('admin.advertiser-rates.index') }}" style="color:#3b82f6;">Add rates first →</a>
                    </div>
                    @endif
                </div>
                <input type="hidden" name="country_rates" id="countryRatesJson" value="{{ $campaign->country_rates ? json_encode($campaign->country_rates) : '' }}">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Target Clicks</label>
                    <input type="number" name="target_clicks" min="1" value="{{ $campaign->target_clicks }}"
                           style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;" required>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Total Value ($)</label>
                    <input type="number" name="total_value" step="0.01" min="0.01" id="totalValueInput" value="{{ $campaign->total_value }}"
                           style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;" required>
                    <div style="font-size:10px;color:#9ca3af;margin-top:3px;">Advance = 50% = $<span id="advancePreview">{{ number_format($campaign->advance_amount,2) }}</span></div>
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:5px;">Admin Note (optional)</label>
                <textarea name="admin_note" rows="2" style="width:100%;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;">{{ $campaign->admin_note }}</textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Save Rates</button>
                @if($campaign->status === 'draft' && $campaign->total_value > 0)
                <button type="button" onclick="document.querySelector('form[action*=send-contract]').submit()" style="flex:1;padding:9px 18px;background:#8b5cf6;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                    Send Contract →
                </button>
                @endif
            </div>
        </form>
    </div>
    @endif

</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Fallback URL --}}
    <div class="card" style="padding:20px;">
        <h3 style="margin:0 0 8px;font-size:15px;font-weight:700;">Fallback URL</h3>
        <p style="font-size:12px;color:var(--text-muted);margin:0 0 12px;">Used when campaign is inactive, completed, or paused.</p>
        @if($campaign->fallback_url)
        <div style="background:#f3f4f6;padding:8px 12px;border-radius:6px;font-size:12px;word-break:break-all;margin-bottom:10px;">
            <a href="{{ $campaign->fallback_url }}" target="_blank" style="color:var(--primary);">{{ $campaign->fallback_url }}</a>
        </div>
        @endif
        <form method="POST" action="{{ route('admin.campaigns.fallback-url', $campaign) }}">
            @csrf
            <div style="display:flex;gap:6px;">
                <input type="url" name="fallback_url" value="{{ $campaign->fallback_url }}" placeholder="https://example.com/fallback"
                       style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;" required>
                <button type="submit" class="btn btn-secondary">Set</button>
            </div>
        </form>
    </div>

    {{-- Assign Tracking Link --}}
    <div class="card" style="padding:20px;">
        <h3 style="margin:0 0 12px;font-size:15px;font-weight:700;">Tracking Links Assigned</h3>
        @if($campaign->trackingLinks->count())
            @foreach($campaign->trackingLinks as $link)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:6px;">
                <div>
                    <span style="font-family:monospace;font-size:12px;font-weight:700;">{{ $link->unique_code }}</span>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $link->tracking_url }}</div>
                </div>
                <form method="POST" action="{{ route('admin.campaigns.unassign-link', $campaign) }}">
                    @csrf
                    <input type="hidden" name="tracking_link_id" value="{{ $link->id }}">
                    <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:4px 8px;border-radius:5px;font-size:11px;cursor:pointer;">Remove</button>
                </form>
            </div>
            @endforeach
        @else
            <p style="font-size:13px;color:var(--text-muted);margin:0 0 12px;">No links assigned yet. <a href="{{ route('admin.tracking.create') }}" style="color:var(--primary);">Create a tracking link →</a></p>
        @endif
        @if($availableLinks->count())
        <form method="POST" action="{{ route('admin.campaigns.assign-link', $campaign) }}">
            @csrf
            <div style="display:flex;gap:6px;margin-top:8px;">
                <select name="tracking_link_id" style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;" required>
                    <option value="">Assign existing link...</option>
                    @foreach($availableLinks as $tlink)
                        <option value="{{ $tlink->id }}">{{ $tlink->unique_code }}{{ $tlink->name ? ' — '.$tlink->name : '' }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
        @endif
    </div>

</div>

{{-- Pending Payments --}}
@php $pendingPayments = $payments->where('status','pending'); @endphp
@if($pendingPayments->count())
<div class="card" style="padding:20px;margin-bottom:20px;border:2px solid #fde68a;background:#fffbeb;">
    <div style="font-size:15px;font-weight:700;color:#92400e;margin-bottom:16px;">
        ⚠️ {{ $pendingPayments->count() }} Pending Payment{{ $pendingPayments->count()>1?'s':'' }} — Awaiting Confirmation
    </div>
    @foreach($pendingPayments as $payment)
    <div style="background:white;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div>
            <div style="font-size:16px;font-weight:800;color:#111827;">${{ number_format($payment->amount,2) }}</div>
            <div style="font-size:12px;color:#6b7280;margin-top:2px;">{{ ucfirst($payment->type) }} · {{ $payment->payment_method ?? '—' }}</div>
            @if($payment->transaction_id)
            <div style="font-size:11px;color:#9ca3af;font-family:monospace;margin-top:2px;">TxID: {{ $payment->transaction_id }}</div>
            @endif
            @if($payment->notes)
            <div style="font-size:12px;color:#374151;margin-top:4px;">{{ $payment->notes }}</div>
            @endif
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $payment->created_at->format('M d, Y H:i') }}</div>
        </div>
        <div style="display:flex;gap:8px;">
            <form method="POST" action="{{ route('admin.campaigns.payments.confirm', $payment) }}">
                @csrf
                <button type="submit" style="padding:8px 16px;background:#10b981;color:white;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">✓ Confirm</button>
            </form>
            <form method="POST" action="{{ route('admin.campaigns.payments.reject', $payment) }}">
                @csrf
                <button type="submit" style="padding:8px 16px;background:#ef4444;color:white;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;" onclick="return confirm('Reject this payment?')">✗ Reject</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- All Payments History --}}
<div class="card" style="padding:20px;margin-bottom:20px;">
    <h3 style="margin:0 0 16px;font-size:15px;font-weight:700;">Payment History</h3>
    @if($payments->count())
    <div style="overflow-x:auto;">
        <table>
            <thead><tr>
                <th>Date</th><th>Type</th><th>Amount</th><th>Method</th><th>Txn ID</th><th>Status</th><th>Confirmed By</th>
            </tr></thead>
            <tbody>
                @foreach($payments as $p)
                @php $psc=['confirmed'=>'#d1fae5|#065f46','rejected'=>'#fee2e2|#991b1b','pending'=>'#fef3c7|#92400e'][$p->status]??'#f3f4f6|#374151'; [$pbg,$pco]=explode('|',$psc); @endphp
                <tr>
                    <td style="font-size:12px;">{{ $p->created_at->format('M d, Y H:i') }}</td>
                    <td style="font-size:12px;">{{ ucfirst($p->type) }}</td>
                    <td style="font-weight:700;">${{ number_format($p->amount,2) }}</td>
                    <td style="font-size:12px;">{{ $p->payment_method ?? '—' }}</td>
                    <td style="font-family:monospace;font-size:11px;color:#9ca3af;">{{ $p->transaction_id ?? '—' }}</td>
                    <td><span style="background:{{ $pbg }};color:{{ $pco }};padding:3px 8px;border-radius:20px;font-size:11px;font-weight:700;">{{ ucfirst($p->status) }}</span></td>
                    <td style="font-size:12px;">{{ $p->confirmedBy?->name ?? '—' }}</td>
                </tr>
                @if($p->notes)
                <tr><td colspan="7" style="font-size:11px;color:#9ca3af;font-style:italic;padding-top:0;">Note: {{ $p->notes }}</td></tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p style="color:var(--text-muted);font-size:13px;margin:0;">No payments submitted yet.</p>
    @endif
</div>

{{-- Country Breakdown --}}
@if($campaign->click_breakdown && count($campaign->click_breakdown))
<div class="card" style="padding:20px;margin-bottom:20px;">
    <h3 style="margin:0 0 16px;font-size:15px;font-weight:700;">Click Breakdown by Country</h3>
    @php $bd = collect($campaign->click_breakdown)->sortDesc()->take(15); $tot = $bd->sum(); @endphp
    <div style="display:flex;flex-wrap:wrap;gap:10px;">
        @foreach($bd as $code=>$cnt)
        <div style="display:flex;align-items:center;gap:8px;background:#f9fafb;border:1px solid #e5e7eb;padding:8px 12px;border-radius:8px;min-width:150px;">
            <img src="https://flagcdn.com/24x18/{{ strtolower($code) }}.png" width="24" height="18" style="border-radius:3px;">
            <span style="font-weight:700;font-size:13px;">{{ strtoupper($code) }}</span>
            <span style="color:var(--text-muted);font-size:12px;">{{ number_format($cnt) }}</span>
            <span style="color:#d1d5db;font-size:11px;">({{ $tot > 0 ? round($cnt/$tot*100,1) : 0 }}%)</span>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function toggleRateFields(type) {
    document.getElementById('fixed-rate-field').style.display = type === 'fixed_rate' ? 'block' : 'none';
    document.getElementById('country-rates-field').style.display = type === 'per_click' ? 'block' : 'none';
}

// Update advance preview when total value changes
document.getElementById('totalValueInput')?.addEventListener('input', function() {
    const advance = (parseFloat(this.value) * 0.5) || 0;
    document.getElementById('advancePreview').textContent = advance.toFixed(2);
});

// Before submitting, collect per-country rates into JSON
document.getElementById('ratesForm')?.addEventListener('submit', function() {
    const inputs = document.querySelectorAll('input[name^="cr["]');
    if (inputs.length) {
        const obj = {};
        inputs.forEach(inp => {
            const code = inp.name.match(/cr\[(\w+)\]/)?.[1];
            if (code && inp.value) obj[code] = parseFloat(inp.value);
        });
        document.getElementById('countryRatesJson').value = JSON.stringify(obj);
        inputs.forEach(i => i.disabled = true); // prevent sending cr[] keys
    }
});

// Load default rates from master rates endpoint
function loadDefaultRates() {
    fetch('{{ route('admin.advertiser-rates.json') }}')
        .then(r => r.json())
        .then(data => {
            data.forEach(r => {
                const inp = document.querySelector(`input[name="cr[${r.country_code}]"]`);
                if (inp) inp.value = r.rate_usd;
            });
        });
}
</script>
@endpush
