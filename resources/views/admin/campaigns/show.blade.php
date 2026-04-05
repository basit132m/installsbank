@extends('layouts.admin')

@section('title', 'Campaign: ' . $campaign->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $campaign->name }}</h1>
        <p class="page-subtitle">
            Advertiser: <a href="{{ route('admin.advertisers.show', $campaign->user) }}" style="color:var(--primary)">{{ $campaign->user->name }}</a>
            &nbsp;|&nbsp;
            <a href="{{ route('admin.campaigns.index') }}" style="color:var(--text-muted)">← Back to Campaigns</a>
        </p>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
        @if($campaign->status === 'active')
            <form method="POST" action="{{ route('admin.campaigns.pause', $campaign) }}">
                @csrf
                <button class="btn btn-secondary">Pause Campaign</button>
            </form>
        @elseif($campaign->status === 'paused')
            <form method="POST" action="{{ route('admin.campaigns.resume', $campaign) }}">
                @csrf
                <button class="btn btn-primary">Resume Campaign</button>
            </form>
        @endif
        @if(!in_array($campaign->status, ['completed','cancelled']))
            <form method="POST" action="{{ route('admin.campaigns.cancel', $campaign) }}" onsubmit="return confirm('Cancel this campaign?')">
                @csrf
                <button class="btn" style="background:#ef4444;color:#fff">Cancel Campaign</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:.75rem 1rem;border-radius:.5rem;margin-bottom:1rem">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:.75rem 1rem;border-radius:.5rem;margin-bottom:1rem">
        {{ session('error') }}
    </div>
@endif

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem">
    <div class="card" style="padding:1rem;text-align:center">
        <div style="font-size:1.5rem;font-weight:700;color:var(--primary)">{{ number_format($campaign->delivered_clicks) }}</div>
        <div style="font-size:.75rem;color:var(--text-muted)">Delivered Clicks</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center">
        <div style="font-size:1.5rem;font-weight:700">{{ number_format($campaign->target_clicks) }}</div>
        <div style="font-size:.75rem;color:var(--text-muted)">Target Clicks</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center">
        <div style="font-size:1.5rem;font-weight:700;color:#f59e0b">${{ number_format($campaign->total_value, 2) }}</div>
        <div style="font-size:.75rem;color:var(--text-muted)">Total Value</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center">
        <div style="font-size:1.5rem;font-weight:700;color:#10b981">${{ number_format($campaign->total_paid, 2) }}</div>
        <div style="font-size:.75rem;color:var(--text-muted)">Total Paid</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center">
        @php
            $statusColors = [
                'draft'           => '#6b7280',
                'pending_payment' => '#f59e0b',
                'active'          => '#10b981',
                'paused'          => '#3b82f6',
                'completed'       => '#8b5cf6',
                'cancelled'       => '#ef4444',
            ];
            $sc = $statusColors[$campaign->status] ?? '#6b7280';
        @endphp
        <div style="font-size:1rem;font-weight:700;color:{{ $sc }}">{{ ucfirst(str_replace('_',' ',$campaign->status)) }}</div>
        <div style="font-size:.75rem;color:var(--text-muted)">Status</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center">
        <div style="font-size:1.5rem;font-weight:700">{{ $campaign->progressPercent() }}%</div>
        <div style="font-size:.75rem;color:var(--text-muted)">Progress</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">

    {{-- Campaign Info --}}
    <div class="card" style="padding:1.25rem">
        <h3 style="margin:0 0 1rem;font-size:1rem">Campaign Details</h3>
        <table style="width:100%;border-collapse:collapse;font-size:.875rem">
            <tr>
                <td style="padding:.4rem 0;color:var(--text-muted);width:45%">Destination URL</td>
                <td style="padding:.4rem 0;word-break:break-all"><a href="{{ $campaign->destination_url }}" target="_blank" style="color:var(--primary)">{{ Str::limit($campaign->destination_url, 50) }}</a></td>
            </tr>
            <tr>
                <td style="padding:.4rem 0;color:var(--text-muted)">Contract Type</td>
                <td style="padding:.4rem 0">{{ $campaign->contract_type === 'fixed_rate' ? 'Fixed Rate' : 'Per-Click (by country)' }}</td>
            </tr>
            @if($campaign->contract_type === 'fixed_rate' && $campaign->fixed_rate)
            <tr>
                <td style="padding:.4rem 0;color:var(--text-muted)">Fixed Rate</td>
                <td style="padding:.4rem 0">${{ $campaign->fixed_rate }} / click</td>
            </tr>
            @endif
            <tr>
                <td style="padding:.4rem 0;color:var(--text-muted)">Advance Required</td>
                <td style="padding:.4rem 0">${{ number_format($campaign->advance_amount, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:.4rem 0;color:var(--text-muted)">Started</td>
                <td style="padding:.4rem 0">{{ $campaign->started_at ? $campaign->started_at->format('M d, Y H:i') : '—' }}</td>
            </tr>
            <tr>
                <td style="padding:.4rem 0;color:var(--text-muted)">Completed</td>
                <td style="padding:.4rem 0">{{ $campaign->completed_at ? $campaign->completed_at->format('M d, Y H:i') : '—' }}</td>
            </tr>
            @if($campaign->admin_note)
            <tr>
                <td style="padding:.4rem 0;color:var(--text-muted)">Admin Note</td>
                <td style="padding:.4rem 0">{{ $campaign->admin_note }}</td>
            </tr>
            @endif
        </table>

        {{-- Progress Bar --}}
        @if($campaign->target_clicks)
        <div style="margin-top:1rem">
            <div style="display:flex;justify-content:space-between;font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem">
                <span>{{ number_format($campaign->delivered_clicks) }} / {{ number_format($campaign->target_clicks) }} clicks</span>
                <span>{{ $campaign->progressPercent() }}%</span>
            </div>
            <div style="background:#e5e7eb;border-radius:999px;height:8px;overflow:hidden">
                <div style="height:100%;border-radius:999px;background:var(--primary);width:{{ $campaign->progressPercent() }}%;transition:width .3s"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Set Rates --}}
    @if(in_array($campaign->status, ['draft','pending_payment','active','paused']))
    <div class="card" style="padding:1.25rem">
        <h3 style="margin:0 0 1rem;font-size:1rem">Set / Update Rates</h3>
        <form method="POST" action="{{ route('admin.campaigns.rates', $campaign) }}">
            @csrf
            <div style="margin-bottom:.75rem">
                <label style="display:block;font-size:.75rem;font-weight:600;margin-bottom:.25rem">Contract Type</label>
                <div style="display:flex;gap:1rem">
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.875rem;cursor:pointer">
                        <input type="radio" name="contract_type" value="fixed_rate" {{ $campaign->contract_type === 'fixed_rate' ? 'checked' : '' }} onchange="toggleRateFields(this.value)"> Fixed Rate
                    </label>
                    <label style="display:flex;align-items:center;gap:.4rem;font-size:.875rem;cursor:pointer">
                        <input type="radio" name="contract_type" value="per_click" {{ $campaign->contract_type === 'per_click' ? 'checked' : '' }} onchange="toggleRateFields(this.value)"> Per-Click (by country)
                    </label>
                </div>
            </div>
            <div id="fixed-rate-field" style="margin-bottom:.75rem;{{ $campaign->contract_type !== 'fixed_rate' ? 'display:none' : '' }}">
                <label style="display:block;font-size:.75rem;font-weight:600;margin-bottom:.25rem">Fixed Rate ($/click)</label>
                <input type="number" name="fixed_rate" step="0.000001" min="0" value="{{ $campaign->fixed_rate }}" class="form-control" style="width:100%;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem">
            </div>
            <div id="country-rates-field" style="margin-bottom:.75rem;{{ $campaign->contract_type !== 'per_click' ? 'display:none' : '' }}">
                <label style="display:block;font-size:.75rem;font-weight:600;margin-bottom:.25rem">Country Rates (JSON: {"US":0.05,"UK":0.03})</label>
                <textarea name="country_rates" rows="4" style="width:100%;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem;font-family:monospace;font-size:.8rem">{{ $campaign->country_rates ? json_encode($campaign->country_rates, JSON_PRETTY_PRINT) : '' }}</textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:.75rem">
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;margin-bottom:.25rem">Target Clicks</label>
                    <input type="number" name="target_clicks" min="1" value="{{ $campaign->target_clicks }}" class="form-control" style="width:100%;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem" required>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;margin-bottom:.25rem">Total Value ($)</label>
                    <input type="number" name="total_value" step="0.01" min="0.01" value="{{ $campaign->total_value }}" class="form-control" style="width:100%;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem" required>
                </div>
            </div>
            <div style="margin-bottom:.75rem">
                <label style="display:block;font-size:.75rem;font-weight:600;margin-bottom:.25rem">Admin Note (optional)</label>
                <textarea name="admin_note" rows="2" style="width:100%;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem;font-size:.875rem">{{ $campaign->admin_note }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Save Rates</button>
        </form>
    </div>
    @endif

</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">

    {{-- Fallback URL --}}
    <div class="card" style="padding:1.25rem">
        <h3 style="margin:0 0 1rem;font-size:1rem">Fallback URL</h3>
        <p style="font-size:.8rem;color:var(--text-muted);margin:0 0 .75rem">Used when campaign is inactive or completed.</p>
        @if($campaign->fallback_url)
            <div style="background:#f3f4f6;padding:.5rem .75rem;border-radius:.375rem;font-size:.8rem;word-break:break-all;margin-bottom:.75rem">
                <a href="{{ $campaign->fallback_url }}" target="_blank" style="color:var(--primary)">{{ $campaign->fallback_url }}</a>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.campaigns.fallback-url', $campaign) }}">
            @csrf
            <div style="display:flex;gap:.5rem">
                <input type="url" name="fallback_url" value="{{ $campaign->fallback_url }}" placeholder="https://example.com/fallback" style="flex:1;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem;font-size:.875rem" required>
                <button type="submit" class="btn btn-secondary" style="white-space:nowrap">Set URL</button>
            </div>
        </form>
    </div>

    {{-- Assign Tracking Link --}}
    <div class="card" style="padding:1.25rem">
        <h3 style="margin:0 0 1rem;font-size:1rem">Tracking Links</h3>
        @if($campaign->trackingLinks->count())
            <div style="margin-bottom:.75rem">
                @foreach($campaign->trackingLinks as $link)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.4rem .5rem;background:#f3f4f6;border-radius:.375rem;margin-bottom:.35rem;font-size:.8rem">
                    <span style="font-family:monospace">{{ $link->slug }}</span>
                    <form method="POST" action="{{ route('admin.campaigns.unassign-link', $campaign) }}" style="margin:0">
                        @csrf
                        <input type="hidden" name="tracking_link_id" value="{{ $link->id }}">
                        <button type="submit" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:.75rem">Remove</button>
                    </form>
                </div>
                @endforeach
            </div>
        @else
            <p style="font-size:.8rem;color:var(--text-muted);margin:0 0 .75rem">No tracking links assigned yet.</p>
        @endif
        <form method="POST" action="{{ route('admin.campaigns.assign-link', $campaign) }}">
            @csrf
            <div style="display:flex;gap:.5rem">
                <select name="tracking_link_id" style="flex:1;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem;font-size:.875rem" required>
                    <option value="">Select tracking link...</option>
                    @foreach(\App\Models\TrackingLink::whereNull('campaign_id')->get() as $tlink)
                        <option value="{{ $tlink->id }}">{{ $tlink->slug }} — {{ Str::limit($tlink->original_url, 40) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary" style="white-space:nowrap">Assign</button>
            </div>
        </form>
    </div>

</div>

{{-- Payments --}}
<div class="card" style="padding:1.25rem;margin-bottom:1.5rem">
    <h3 style="margin:0 0 1rem;font-size:1rem">Payment History</h3>
    @if($payments->count())
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:.875rem">
            <thead>
                <tr style="border-bottom:2px solid #e5e7eb">
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Date</th>
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Type</th>
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Amount</th>
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Method</th>
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Txn ID</th>
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Status</th>
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Confirmed By</th>
                    <th style="padding:.5rem .75rem;text-align:left;color:var(--text-muted);font-weight:600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:.5rem .75rem">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                    <td style="padding:.5rem .75rem">{{ ucfirst($payment->type) }}</td>
                    <td style="padding:.5rem .75rem;font-weight:600">${{ number_format($payment->amount, 2) }}</td>
                    <td style="padding:.5rem .75rem">{{ $payment->payment_method ?? '—' }}</td>
                    <td style="padding:.5rem .75rem;font-family:monospace;font-size:.75rem">{{ $payment->transaction_id ?? '—' }}</td>
                    <td style="padding:.5rem .75rem">
                        @if($payment->status === 'confirmed')
                            <span style="background:#d1fae5;color:#065f46;padding:.2rem .5rem;border-radius:999px;font-size:.7rem;font-weight:600">Confirmed</span>
                        @elseif($payment->status === 'rejected')
                            <span style="background:#fee2e2;color:#991b1b;padding:.2rem .5rem;border-radius:999px;font-size:.7rem;font-weight:600">Rejected</span>
                        @else
                            <span style="background:#fef3c7;color:#92400e;padding:.2rem .5rem;border-radius:999px;font-size:.7rem;font-weight:600">Pending</span>
                        @endif
                    </td>
                    <td style="padding:.5rem .75rem">{{ $payment->confirmedBy?->name ?? '—' }}</td>
                    <td style="padding:.5rem .75rem">
                        @if($payment->status === 'pending')
                        <div style="display:flex;gap:.35rem">
                            <form method="POST" action="{{ route('admin.campaigns.payments.confirm', $payment) }}">
                                @csrf
                                <button type="submit" style="background:#10b981;color:#fff;border:none;padding:.25rem .6rem;border-radius:.25rem;font-size:.75rem;cursor:pointer">Confirm</button>
                            </form>
                            <form method="POST" action="{{ route('admin.campaigns.payments.reject', $payment) }}">
                                @csrf
                                <button type="submit" style="background:#ef4444;color:#fff;border:none;padding:.25rem .6rem;border-radius:.25rem;font-size:.75rem;cursor:pointer" onclick="return confirm('Reject this payment?')">Reject</button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @if($payment->notes)
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td colspan="8" style="padding:.25rem .75rem;font-size:.75rem;color:var(--text-muted);font-style:italic">
                        Note: {{ $payment->notes }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p style="color:var(--text-muted);font-size:.875rem;margin:0">No payments submitted yet.</p>
    @endif
</div>

{{-- Country Breakdown --}}
@if($campaign->click_breakdown && count($campaign->click_breakdown))
<div class="card" style="padding:1.25rem;margin-bottom:1.5rem">
    <h3 style="margin:0 0 1rem;font-size:1rem">Click Breakdown by Country</h3>
    @php
        $breakdown = collect($campaign->click_breakdown)->sortDesc()->take(15);
        $totalClicks = $breakdown->sum();
    @endphp
    <div style="display:flex;flex-wrap:wrap;gap:.75rem">
        @foreach($breakdown as $code => $cnt)
        <div style="display:flex;align-items:center;gap:.5rem;background:#f9fafb;border:1px solid #e5e7eb;padding:.4rem .75rem;border-radius:.5rem;min-width:140px">
            <img src="https://flagcdn.com/24x18/{{ strtolower($code) }}.png" width="24" height="18" alt="{{ $code }}" style="border-radius:2px">
            <span style="font-weight:600;font-size:.875rem">{{ strtoupper($code) }}</span>
            <span style="color:var(--text-muted);font-size:.8rem">{{ number_format($cnt) }}</span>
            <span style="color:var(--text-muted);font-size:.75rem">({{ $totalClicks > 0 ? round($cnt/$totalClicks*100,1) : 0 }}%)</span>
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
</script>
@endpush
