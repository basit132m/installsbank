@extends('layouts.publisher')
@section('title', 'My Contracts')
@section('page-title', 'Contracts')

@section('content')

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

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fff1f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#991b1b;font-weight:600;">
    {{ session('error') }}
</div>
@endif

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:4px;">My Contracts</h2>
        <p style="font-size:14px;color:#6b7280;">View your contract type, request a change, and review your history.</p>
    </div>
    @if($ctLabel)
    <div style="display:flex;align-items:center;gap:8px;background:{{ $ctColor['bg'] }};border:1px solid {{ $ctColor['border'] }};border-radius:10px;padding:10px 16px;">
        <div style="width:9px;height:9px;border-radius:50%;background:{{ $ctColor['dot'] }};flex-shrink:0;"></div>
        <span style="font-size:13px;font-weight:700;color:{{ $ctColor['text'] }};">Active Contract: {{ $ctLabel }}</span>
    </div>
    @endif
</div>

{{-- Contract type info cards --}}
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
        <div style="padding:18px 22px;">
            <p style="font-size:13px;color:#4b5563;line-height:1.7;margin-bottom:0;">
                Earn a rate per <strong>1,000 valid unique Windows clicks</strong>. Rate varies by visitor country. Duplicate IPs within 24 h and fraud traffic are filtered automatically.
            </p>
        </div>
    </div>

    {{-- Fixed Daily --}}
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
        <div style="padding:18px 22px;">
            @if($ct === 'fixed' && $profile?->fixed_daily_rate)
            <div style="font-size:24px;font-weight:800;color:#166534;margin-bottom:6px;">${{ number_format($profile->fixed_daily_rate, 4) }}<span style="font-size:13px;font-weight:600;color:#6b7280;">/day</span></div>
            @endif
            <p style="font-size:13px;color:#4b5563;line-height:1.7;margin-bottom:0;">
                A <strong>fixed amount is credited daily at 00:05 UTC</strong> regardless of click volume. Offered to verified high-traffic publishers.
            </p>
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
        <div style="padding:18px 22px;">
            <p style="font-size:13px;color:#4b5563;line-height:1.7;margin-bottom:0;">
                Earn for each <strong>verified app install</strong> from your traffic. Performance-based, rate is set individually in your contract offer.
            </p>
        </div>
    </div>

</div>

{{-- Request Contract Change --}}
@if($ct && $profile)
<div class="card" style="margin-bottom:28px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
        <div>
            <div class="card-title">Request Contract Change</div>
            <p style="font-size:13px;color:#6b7280;margin-top:4px;">Submit a request to switch to a different contract type. Admin will review and respond.</p>
        </div>
        @if($hasPendingRequest)
        <span style="background:#fef3c7;border:1px solid #fcd34d;border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;color:#92400e;white-space:nowrap;">
            ⏳ Request Pending Review
        </span>
        @endif
    </div>

    @if($hasPendingRequest)
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;font-size:13px;color:#78350f;">
        You have a pending contract change request. You can submit a new one once admin responds.
    </div>
    @else
    <form method="POST" action="{{ route('publisher.contract-change.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Current Contract</label>
                <div style="padding:10px 14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-weight:600;color:#374151;">
                    {{ $ctLabel }}
                </div>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Request Change To</label>
                <select name="requested_type" class="form-control" required>
                    <option value="">— Select contract type —</option>
                    @if($ct !== 'per_click')
                    <option value="per_click">Per-Click</option>
                    @endif
                    @if($ct !== 'fixed')
                    <option value="fixed">Fixed Daily Rate</option>
                    @endif
                    @if($ct !== 'installs_base')
                    <option value="installs_base">Installs Base</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">Reason <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
            <textarea name="reason" class="form-control" rows="3" maxlength="1000" placeholder="Briefly explain why you want to change your contract type..."></textarea>
        </div>
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#78350f;">
            <strong>Important:</strong> If approved, your contract type will change immediately and tracking data will restart from the change date. Your previous earnings and contract history are preserved.
        </div>
        <button type="submit" class="btn btn-primary">Submit Change Request</button>
    </form>
    @endif
</div>
@endif

{{-- Contract History --}}
@if($contracts->isNotEmpty())
<div class="card" style="margin-bottom:28px;">
    <div class="card-title" style="margin-bottom:16px;">Contract Offers History</div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Type</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Rate</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Offered</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
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
                        @if($c->rate && $c->type === 'fixed') ${{ number_format($c->rate, 4) }}/day
                        @elseif($c->rate && $c->type === 'per_click') ${{ number_format($c->rate, 4) }}/1k clicks
                        @else —
                        @endif
                    </td>
                    <td style="padding:10px 12px;color:#6b7280;">{{ $c->created_at->format('M d, Y') }}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $cStatusColor['bg'] }};color:{{ $cStatusColor['text'] }};">{{ ucfirst($cStatus) }}</span>
                        @if($cStatus === 'pending')
                        <span style="margin-left:8px;display:inline-flex;gap:6px;">
                            <form method="POST" action="{{ route('publisher.contract.accept', $c) }}" style="display:inline;">@csrf
                                <button type="submit" style="background:#01BF63;color:white;border:none;border-radius:6px;padding:4px 12px;font-size:12px;font-weight:600;cursor:pointer;">Accept</button>
                            </form>
                            <form method="POST" action="{{ route('publisher.contract.reject', $c) }}" style="display:inline;" onsubmit="return confirm('Decline this contract offer?')">@csrf
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

{{-- Change Request History --}}
@if($changeRequests->isNotEmpty())
<div class="card" style="margin-bottom:28px;">
    <div class="card-title" style="margin-bottom:16px;">Change Request History</div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">From</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Requested</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Date</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Admin Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($changeRequests as $r)
                @php
                    $rLabel = fn($t) => match($t) { 'per_click' => 'Per-Click', 'fixed' => 'Fixed Daily', 'installs_base' => 'Installs Base', default => ucfirst($t) };
                    $rStatusColor = match($r->status) {
                        'approved' => ['bg'=>'#d1fae5','text'=>'#065f46'],
                        'rejected' => ['bg'=>'#fee2e2','text'=>'#991b1b'],
                        default    => ['bg'=>'#fef3c7','text'=>'#92400e'],
                    };
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 12px;color:#374151;">{{ $rLabel($r->current_type) }}</td>
                    <td style="padding:10px 12px;font-weight:600;color:#111827;">{{ $rLabel($r->requested_type) }}</td>
                    <td style="padding:10px 12px;color:#6b7280;">{{ $r->created_at->format('M d, Y') }}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $rStatusColor['bg'] }};color:{{ $rStatusColor['text'] }};">{{ ucfirst($r->status) }}</span>
                    </td>
                    <td style="padding:10px 12px;color:#6b7280;font-size:12px;">{{ $r->admin_note ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Previous contract snapshots --}}
@if($snapshots->isNotEmpty())
<div class="card" style="margin-bottom:28px;">
    <div class="card-title" style="margin-bottom:4px;">Previous Contract Data</div>
    <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">Earnings and stats from your previous contract periods, preserved when you switched.</p>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Was</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Changed To</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Earnings at Change</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Clicks at Change</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Changed On</th>
                </tr>
            </thead>
            <tbody>
                @foreach($snapshots as $s)
                @php $sLabel = fn($t) => match($t) { 'per_click' => 'Per-Click', 'fixed' => 'Fixed Daily', 'installs_base' => 'Installs Base', default => ucfirst($t) }; @endphp
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 12px;color:#374151;">{{ $sLabel($s->contract_type) }}</td>
                    <td style="padding:10px 12px;font-weight:600;color:#111827;">{{ $sLabel($s->changed_to) }}</td>
                    <td style="padding:10px 12px;font-weight:700;color:#01BF63;">${{ number_format($s->total_earnings, 4) }}</td>
                    <td style="padding:10px 12px;color:#374151;">{{ number_format($s->total_clicks) }}</td>
                    <td style="padding:10px 12px;color:#6b7280;">{{ $s->changed_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- FAQ --}}
<div>
    <h3 style="font-size:16px;font-weight:700;color:#111827;margin-bottom:16px;">Frequently Asked Questions</h3>
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach([
            ['q' => 'How does the 48-hour test period work?', 'a' => 'When your account is approved, you run a 48-hour test where your live traffic is evaluated for quality. Afterwards, our team sends you a contract offer based on your results. Your 2-day test earnings are paid out when you accept (or even decline) a fixed contract offer.'],
            ['q' => 'Why are some clicks not counted?', 'a' => 'Only unique Windows desktop clicks are credited. Mobile, Mac, and Linux clicks are excluded. Duplicate IPs within 24 hours are filtered, as are clicks flagged as bot or fraud.'],
            ['q' => 'What happens when my contract type changes?', 'a' => 'A snapshot of your current earnings and click data is saved (visible in "Previous Contract Data" above). Tracking then restarts under the new contract type. Your balance is not affected.'],
            ['q' => 'When are Fixed Daily Rate earnings credited?', 'a' => 'Fixed daily amounts are automatically added to your balance at 00:05 UTC every day.'],
            ['q' => 'Can I request any contract type?', 'a' => 'You can request any of the three types. Our team will review whether it is appropriate for your traffic profile and respond. Note that Fixed Daily Rate is typically reserved for verified high-volume publishers.'],
        ] as $faq)
        <details style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;cursor:pointer;">
            <summary style="padding:16px 20px;font-size:14px;font-weight:600;color:#111827;list-style:none;display:flex;justify-content:space-between;align-items:center;user-select:none;">
                {{ $faq['q'] }}
                <svg width="16" height="16" fill="none" stroke="#6b7280" viewBox="0 0 24 24" style="flex-shrink:0;margin-left:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <div style="padding:0 20px 16px;font-size:14px;color:#6b7280;line-height:1.7;border-top:1px solid #f3f4f6;">{{ $faq['a'] }}</div>
        </details>
        @endforeach
    </div>
</div>
<style>details[open] summary svg{transform:rotate(180deg)} details summary::-webkit-details-marker{display:none}</style>
@endsection
