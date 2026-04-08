@extends('layouts.publisher')
@section('title', 'My Contracts')
@section('page-title', 'Contracts')

@section('content')

{{-- Current contract status --}}
@php
    $ct = $profile?->contract_type ?? null;
    $ctLabel = match($ct) {
        'per_click'     => 'Per-Click',
        'fixed'         => 'Fixed Daily Rate',
        'installs_base' => 'Installs Base',
        default         => null,
    };
    $ctColor = match($ct) {
        'per_click'     => ['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1e40af','dot'=>'#3b82f6'],
        'fixed'         => ['bg'=>'#f0fdf4','border'=>'#bbf7d0','text'=>'#166534','dot'=>'#16a34a'],
        'installs_base' => ['bg'=>'#faf5ff','border'=>'#e9d5ff','text'=>'#6b21a8','dot'=>'#7c3aed'],
        default         => ['bg'=>'#f9fafb','border'=>'#e5e7eb','text'=>'#6b7280','dot'=>'#9ca3af'],
    };
@endphp

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:4px;">How Contracts Work</h2>
        <p style="font-size:14px;color:#6b7280;">Understand each contract type and what it means for your earnings.</p>
    </div>
    @if($ctLabel)
    <div style="display:flex;align-items:center;gap:8px;background:{{ $ctColor['bg'] }};border:1px solid {{ $ctColor['border'] }};border-radius:10px;padding:10px 16px;">
        <div style="width:9px;height:9px;border-radius:50%;background:{{ $ctColor['dot'] }};flex-shrink:0;"></div>
        <span style="font-size:13px;font-weight:700;color:{{ $ctColor['text'] }};">Your Contract: {{ $ctLabel }}</span>
    </div>
    @endif
</div>

{{-- Contract type cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-bottom:32px;">

    {{-- Per-Click --}}
    <div style="background:white;border:2px solid {{ $ct === 'per_click' ? '#3b82f6' : '#e5e7eb' }};border-radius:16px;overflow:hidden;position:relative;">
        @if($ct === 'per_click')
        <div style="position:absolute;top:14px;right:14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;color:#1e40af;">YOUR PLAN</div>
        @endif
        <div style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);padding:22px 24px 18px;">
            <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
            </div>
            <div style="font-size:18px;font-weight:800;color:white;margin-bottom:4px;">Per-Click</div>
            <div style="font-size:13px;color:rgba(255,255,255,0.8);">Earn for every 1,000 valid clicks</div>
        </div>
        <div style="padding:22px 24px;">
            <p style="font-size:14px;color:#374151;line-height:1.7;margin-bottom:16px;">
                You earn a rate per <strong>1,000 valid unique Windows clicks</strong> that pass through your ad code. The rate depends on the visitor's country.
            </p>
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:18px;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Rate is set per country — higher-value geos earn more per 1,000 clicks</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Only Windows users are counted — mobile, Mac and Linux clicks do not earn</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Duplicate clicks from the same IP within 24 hours are not counted</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Fraudulent or bot traffic is automatically filtered out</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Earnings update in real-time on your dashboard as clicks come in</span>
                </div>
            </div>
            <div style="background:#eff6ff;border-radius:10px;padding:12px 14px;">
                <div style="font-size:12px;font-weight:700;color:#1e40af;margin-bottom:4px;">HOW EARNINGS ARE CALCULATED</div>
                <div style="font-size:13px;color:#1e40af;font-family:monospace;">
                    Earnings = (Valid Clicks ÷ 1,000) × Country Rate ($)
                </div>
            </div>
        </div>
    </div>

    {{-- Fixed Daily Rate --}}
    <div style="background:white;border:2px solid {{ $ct === 'fixed' ? '#16a34a' : '#e5e7eb' }};border-radius:16px;overflow:hidden;position:relative;">
        @if($ct === 'fixed')
        <div style="position:absolute;top:14px;right:14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;color:#166534;">YOUR PLAN</div>
        @endif
        <div style="background:linear-gradient(135deg,#01BF63,#00874a);padding:22px 24px 18px;">
            <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div style="font-size:18px;font-weight:800;color:white;margin-bottom:4px;">Fixed Daily Rate</div>
            <div style="font-size:13px;color:rgba(255,255,255,0.8);">Predictable income every day</div>
        </div>
        <div style="padding:22px 24px;">
            <p style="font-size:14px;color:#374151;line-height:1.7;margin-bottom:16px;">
                You receive a <strong>fixed amount credited to your balance every day</strong> at midnight, regardless of how many clicks you receive that day. Offered to verified high-volume publishers.
            </p>
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:18px;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Daily rate is agreed upon in your contract and remains fixed</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Credit is added automatically at 00:05 every day</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Your ad code must remain active and embedded — rate can be revised if traffic drops significantly</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">The 2-day test period is paid out when you accept your fixed contract</span>
                </div>
            </div>
            @if($ct === 'fixed' && $profile?->fixed_daily_rate)
            <div style="background:#f0fdf4;border-radius:10px;padding:12px 14px;">
                <div style="font-size:12px;font-weight:700;color:#166534;margin-bottom:4px;">YOUR DAILY RATE</div>
                <div style="font-size:22px;font-weight:800;color:#166534;">${{ number_format($profile->fixed_daily_rate, 4) }}</div>
                <div style="font-size:12px;color:#166534;margin-top:2px;">credited daily at 00:05 UTC</div>
            </div>
            @else
            <div style="background:#f0fdf4;border-radius:10px;padding:12px 14px;">
                <div style="font-size:12px;font-weight:700;color:#166534;margin-bottom:4px;">DAILY EARNINGS</div>
                <div style="font-size:13px;color:#166534;">Rate is agreed individually — contact support for details</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Installs Base --}}
    <div style="background:white;border:2px solid {{ $ct === 'installs_base' ? '#7c3aed' : '#e5e7eb' }};border-radius:16px;overflow:hidden;position:relative;">
        @if($ct === 'installs_base')
        <div style="position:absolute;top:14px;right:14px;background:#faf5ff;border:1px solid #e9d5ff;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;color:#6b21a8;">YOUR PLAN</div>
        @endif
        <div style="background:linear-gradient(135deg,#7c3aed,#5b21b6);padding:22px 24px 18px;">
            <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
                <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </div>
            <div style="font-size:18px;font-weight:800;color:white;margin-bottom:4px;">Installs Base</div>
            <div style="font-size:13px;color:rgba(255,255,255,0.8);">Earn per verified app install</div>
        </div>
        <div style="padding:22px 24px;">
            <p style="font-size:14px;color:#374151;line-height:1.7;margin-bottom:16px;">
                You earn for each <strong>verified app install</strong> that originates from your traffic. This is a performance-based contract for publishers who drive high-quality install traffic.
            </p>
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:18px;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Earnings are tracked per install, not per click</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Only installs verified as legitimate are credited</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Install counts visible in the "Installs" section on your dashboard</span>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:20px;height:20px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg width="11" height="11" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span style="font-size:13px;color:#4b5563;">Rate per install is set in your individual contract offer</span>
                </div>
            </div>
            <div style="background:#faf5ff;border-radius:10px;padding:12px 14px;">
                <div style="font-size:12px;font-weight:700;color:#6b21a8;margin-bottom:4px;">CONTACT SUPPORT</div>
                <div style="font-size:13px;color:#6b21a8;">For rate details and install tracking questions, open a support ticket</div>
            </div>
        </div>
    </div>

</div>

{{-- Contract history --}}
@if($contracts->isNotEmpty())
<div class="card">
    <div class="card-title" style="margin-bottom:16px;">Contract History</div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="text-align:left;padding:8px 12px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Type</th>
                    <th style="text-align:left;padding:8px 12px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Rate</th>
                    <th style="text-align:left;padding:8px 12px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Offered</th>
                    <th style="text-align:left;padding:8px 12px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $c)
                @php
                    $cTypeLabel = match($c->type) {
                        'per_click'     => 'Per-Click',
                        'fixed'         => 'Fixed Daily',
                        'installs_base' => 'Installs Base',
                        default         => ucfirst($c->type),
                    };
                    $cStatus = $c->status ?? 'pending';
                    $cStatusColor = match($cStatus) {
                        'accepted' => ['bg'=>'#d1fae5','text'=>'#065f46'],
                        'rejected' => ['bg'=>'#fee2e2','text'=>'#991b1b'],
                        default    => ['bg'=>'#fef3c7','text'=>'#92400e'],
                    };
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 12px;font-weight:600;color:#111827;">{{ $cTypeLabel }}</td>
                    <td style="padding:10px 12px;color:#374151;">
                        @if($c->rate && $c->type === 'fixed')
                            ${{ number_format($c->rate, 4) }}/day
                        @elseif($c->rate && $c->type === 'per_click')
                            ${{ number_format($c->rate, 4) }}/1k clicks
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding:10px 12px;color:#6b7280;">{{ $c->created_at->format('M d, Y') }}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $cStatusColor['bg'] }};color:{{ $cStatusColor['text'] }};">
                            {{ ucfirst($cStatus) }}
                        </span>
                        @if($cStatus === 'pending')
                        <span style="margin-left:10px;display:inline-flex;gap:8px;">
                            <form method="POST" action="{{ route('publisher.contract.accept', $c) }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:#01BF63;color:white;border:none;border-radius:6px;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('publisher.contract.reject', $c) }}" style="display:inline;" onsubmit="return confirm('Decline this contract offer?')">
                                @csrf
                                <button type="submit" style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:6px;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;">Decline</button>
                            </form>
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- FAQ --}}
<div style="margin-top:28px;">
    <h3 style="font-size:16px;font-weight:700;color:#111827;margin-bottom:16px;">Frequently Asked Questions</h3>
    <div style="display:flex;flex-direction:column;gap:10px;">

        @foreach([
            ['q' => 'How does the 48-hour test period work?', 'a' => 'When your account is approved, you run a 48-hour test where your live traffic is evaluated for quality. Afterwards, our team sends you a contract offer tailored to your audience. If a fixed contract is offered and you accept (or even decline), your 2-day test earnings are added to your balance.'],
            ['q' => 'Why are some of my clicks not counted?', 'a' => 'Only unique Windows desktop clicks are credited. Clicks from mobile devices, Mac, or Linux are excluded. Duplicate visits from the same IP within 24 hours are also filtered, as are clicks flagged as bot or fraud traffic.'],
            ['q' => 'Can my contract type change?', 'a' => 'Yes. As your traffic grows and matures, our team may offer you an upgraded contract. All new offers appear in the Contract History table above with Accept / Decline buttons.'],
            ['q' => 'When are Fixed Daily Rate earnings credited?', 'a' => 'Fixed daily amounts are automatically added to your balance at 00:05 UTC every day. You can see each credit in your Stats page.'],
            ['q' => 'What happens if I decline a contract offer?', 'a' => 'Declining does not close your account. Our team may send a revised offer. If the offer follows a completed test period, you still receive your test period earnings even after declining.'],
            ['q' => 'When can I withdraw my earnings?', 'a' => 'Withdrawals are available once your balance reaches the minimum threshold. You can submit a request from the Withdrawals page. Payments are processed manually by our team.'],
        ] as $faq)
        <details style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;cursor:pointer;">
            <summary style="padding:16px 20px;font-size:14px;font-weight:600;color:#111827;list-style:none;display:flex;justify-content:space-between;align-items:center;user-select:none;">
                {{ $faq['q'] }}
                <svg width="16" height="16" fill="none" stroke="#6b7280" viewBox="0 0 24 24" style="flex-shrink:0;margin-left:12px;transition:transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <div style="padding:0 20px 16px;font-size:14px;color:#6b7280;line-height:1.7;border-top:1px solid #f3f4f6;">
                {{ $faq['a'] }}
            </div>
        </details>
        @endforeach
    </div>
</div>

<style>
details[open] summary svg { transform: rotate(180deg); }
details summary::-webkit-details-marker { display:none; }
</style>
@endsection
