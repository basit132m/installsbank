@extends('layouts.admin')
@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
<div style="display:flex;gap:8px;align-items:center;margin-bottom:20px;">
    <a href="{{ route('admin.publishers.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    <a href="{{ route('admin.publishers.stats', $user) }}" class="btn btn-ghost btn-sm" style="color:#3b82f6;border-color:#3b82f6;">📊 Detailed Stats</a>
    <span class="badge {{ $user->status === 'active' ? 'badge-success' : ($user->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">{{ ucfirst($user->status) }}</span>
    @if($user->status === 'pending')
        <form method="POST" action="{{ route('admin.publishers.activate', $user) }}" style="display:inline;">@csrf<button class="btn btn-success btn-sm">Activate Publisher</button></form>
    @elseif($user->status === 'active')
        <form method="POST" action="{{ route('admin.publishers.suspend', $user) }}" style="display:inline;" onsubmit="return confirm('Suspend this publisher?')">@csrf<button class="btn btn-danger btn-sm">Suspend</button></form>
    @endif
</div>

<!-- Publisher Info + Quick Stats -->
<div class="grid-2 mb-6">
    <div class="card">
        <div class="card-title mb-4">Publisher Information</div>
        <table style="width:100%;">
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;width:40%;">Email</td><td style="font-size:14px;">{{ $user->email }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Phone</td><td style="font-size:14px;">{{ $user->phone ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Website</td><td style="font-size:14px;"><a href="{{ $user->website }}" target="_blank" style="color:#01BF63;">{{ $user->website ?? '—' }}</a></td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Registered</td><td style="font-size:14px;">{{ $user->created_at->format('M d, Y H:i') }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Last Login</td><td style="font-size:14px;">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Contract</td><td><span class="badge {{ $user->publisherProfile?->contract_type !== 'none' ? 'badge-primary' : 'badge-gray' }}">{{ ucfirst($user->publisherProfile?->contract_type ?? 'none') }}</span></td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Payment Enabled</td><td><span class="badge {{ $user->publisherProfile?->payment_enabled ? 'badge-success' : 'badge-gray' }}">{{ $user->publisherProfile?->payment_enabled ? 'Yes' : 'No' }}</span></td></tr>
        </table>
    </div>

    <div class="card">
        <div class="card-title mb-4">Click Statistics</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="background:#f9fafb;border-radius:8px;padding:14px;">
                <div style="font-size:22px;font-weight:800;color:#01BF63;">{{ number_format($clickStats['today']) }}</div>
                <div style="font-size:12px;color:#6b7280;">Valid Clicks Today</div>
            </div>
            <div style="background:#fff7ed;border-radius:8px;padding:14px;">
                <div style="font-size:22px;font-weight:800;color:#f59e0b;">{{ number_format($clickStats['today_windows']) }}</div>
                <div style="font-size:12px;color:#6b7280;">Windows Clicks Today (shown)</div>
            </div>
            <div style="background:#f0fdf4;border-radius:8px;padding:14px;">
                <div style="font-size:22px;font-weight:800;color:#111827;">{{ number_format($clickStats['total_actual_windows']) }}</div>
                <div style="font-size:12px;color:#6b7280;font-weight:600;">Actual Windows (Admin View)</div>
            </div>
            <div style="background:#fee2e2;border-radius:8px;padding:14px;">
                <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ number_format($clickStats['today_fraud']) }}</div>
                <div style="font-size:12px;color:#6b7280;">Fraud Clicks Today</div>
            </div>
            <div style="background:#f9fafb;border-radius:8px;padding:14px;">
                <div style="font-size:22px;font-weight:800;">{{ number_format($clickStats['this_week']) }}</div>
                <div style="font-size:12px;color:#6b7280;">This Week</div>
            </div>
            <div style="background:#f9fafb;border-radius:8px;padding:14px;">
                <div style="font-size:22px;font-weight:800;">{{ number_format($clickStats['total']) }}</div>
                <div style="font-size:12px;color:#6b7280;">All Time Valid</div>
            </div>
        </div>
    </div>
</div>

<!-- Click Chart -->
<div class="card mb-6">
    <div class="card-title mb-4">Click History (Last 14 Days)</div>
    <div id="publisherClickChart"></div>
</div>

<!-- Management Actions Row -->
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
        <div class="card-subtitle mb-4" style="font-size:12px;">Status: <strong>{{ ucfirst($user->publisherProfile?->test_status ?? 'not_started') }}</strong></div>
        @if($user->publisherProfile?->test_total_clicks !== null)
            <div style="background:#e6faf2;padding:12px;border-radius:8px;margin-bottom:12px;">
                <div style="font-size:20px;font-weight:800;color:#01BF63;">{{ number_format($user->publisherProfile->test_total_clicks) }}</div>
                <div style="font-size:12px;color:#6b7280;">Total Clicks Entered</div>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.publishers.test-results', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Enter Total Clicks</label>
                <input type="number" name="test_total_clicks" class="form-control" value="{{ $user->publisherProfile?->test_total_clicks ?? '' }}" placeholder="e.g. 5200" min="0">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Results</button>
        </form>
    </div>

    <!-- Offer Contract -->
    <div class="card">
        <div class="card-title mb-1">Offer Contract</div>
        <div class="card-subtitle mb-4" style="font-size:12px;">Publisher will accept or reject in their dashboard.</div>
        @php $pendingContract = $user->contracts()->where('status','pending')->first(); @endphp
        @if($pendingContract)
            <div class="alert" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">
                Pending contract: {{ ucfirst($pendingContract->type) }} at ${{ $pendingContract->rate }}
            </div>
        @else
        <form method="POST" action="{{ route('admin.contracts.offer', $user) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Contract Type</label>
                <select name="type" class="form-control form-select">
                    <option value="per_click">Per 1,000 Unique Clicks</option>
                    <option value="fixed">Fixed Daily Rate</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Rate (USD)</label>
                <input type="number" name="rate" class="form-control" placeholder="0.0000" step="0.0001" min="0.0001" required>
            </div>
            <div class="form-group">
                <label class="form-label">Note (optional)</label>
                <textarea name="admin_note" class="form-control" rows="2" placeholder="Internal note..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Send Contract Offer</button>
        </form>
        @endif
    </div>
</div>

<!-- Payment Settings -->
<div class="card mb-6">
    <div class="flex-between">
        <div>
            <div class="card-title">Payment & Earnings Settings</div>
            <div class="card-subtitle">Balance: <strong style="color:#01BF63;">${{ number_format($user->publisherProfile?->balance ?? 0, 4) }}</strong> · Total Earned: <strong>${{ number_format($user->publisherProfile?->total_earnings ?? 0, 4) }}</strong></div>
        </div>
        <form method="POST" action="{{ route('admin.publishers.payment-status', $user) }}">
            @csrf
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;font-weight:500;">Enable Payments:</span>
                <label class="toggle"><input type="checkbox" name="payment_enabled" value="1" {{ $user->publisherProfile?->payment_enabled ? 'checked' : '' }} onchange="this.form.submit()"><span class="toggle-slider"></span></label>
            </div>
        </form>
    </div>
</div>

<!-- Publisher Tags -->
<div class="card mb-6">
    <div class="card-title mb-3">Publisher Tags</div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
        @forelse($user->publisherTags as $tag)
        @php
            $tagColors = ['green'=>'#01BF63','blue'=>'#3b82f6','red'=>'#ef4444','amber'=>'#f59e0b','gray'=>'#6b7280','purple'=>'#8b5cf6'];
            $tc = $tagColors[$tag->color] ?? '#6b7280';
        @endphp
        <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $tc }}1a;color:{{ $tc }};border:1px solid {{ $tc }}40;padding:4px 12px;border-radius:20px;font-size:13px;font-weight:600;">
            {{ $tag->tag }}
            <form method="POST" action="{{ route('admin.publishers.tags.remove', $user) }}" style="display:inline;">
                @csrf @method('DELETE')
                <input type="hidden" name="tag" value="{{ $tag->tag }}">
                <button type="submit" style="background:none;border:none;cursor:pointer;color:{{ $tc }};font-size:14px;line-height:1;padding:0;">×</button>
            </form>
        </span>
        @empty
        <span style="color:#9ca3af;font-size:13px;">No tags yet</span>
        @endforelse
    </div>
    <form method="POST" action="{{ route('admin.publishers.tags.add', $user) }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
        @csrf
        <div class="form-group" style="margin:0;flex:1;min-width:150px;">
            <label class="form-label">Tag Name</label>
            <input type="text" name="tag" class="form-control" placeholder="e.g. Trusted, VIP, Review" maxlength="50" required>
        </div>
        <div class="form-group" style="margin:0;">
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
        <button type="submit" class="btn btn-primary">Add Tag</button>
    </form>
</div>

<!-- Fraud Detection Settings -->
<div class="card mb-6">
    <div class="card-title mb-1">Fraud Detection Settings</div>
    <div class="card-subtitle mb-4" style="font-size:12px;">These checks only apply to this publisher. Enable only what is needed to avoid false positives.</div>
    <form method="POST" action="{{ route('admin.publishers.fraud-settings', $user) }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
            <div>
                <div class="toggle-wrap mb-3">
                    <label class="toggle"><input type="checkbox" name="fraud_headless_browser" value="1" {{ $user->publisherProfile?->fraud_headless_browser ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    <div>
                        <span style="font-size:13px;font-weight:600;">Headless Browser Detection</span>
                        <div style="font-size:11px;color:#9ca3af;">Blocks automation tools (Selenium, Puppeteer, PhantomJS)</div>
                    </div>
                </div>
                <div class="toggle-wrap mb-3">
                    <label class="toggle"><input type="checkbox" name="fraud_country_mismatch" value="1" {{ $user->publisherProfile?->fraud_country_mismatch ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    <div>
                        <span style="font-size:13px;font-weight:600;">Country Mismatch Detection</span>
                        <div style="font-size:11px;color:#9ca3af;">Flags clicks where IP country doesn't match browser language</div>
                    </div>
                </div>
                <div class="toggle-wrap">
                    <label class="toggle"><input type="checkbox" name="fraud_suspicious_referrer" value="1" {{ $user->publisherProfile?->fraud_suspicious_referrer ? 'checked' : '' }}><span class="toggle-slider"></span></label>
                    <div>
                        <span style="font-size:13px;font-weight:600;">Suspicious Referrer Detection</span>
                        <div style="font-size:11px;color:#9ca3af;">Blocks traffic from known exchanges, PTC, and bot farms</div>
                    </div>
                </div>
            </div>
            <div>
                <label class="form-label">Country Whitelist (comma-separated codes)</label>
                <input type="text" name="allowed_countries" class="form-control" style="margin-bottom:6px;"
                    value="{{ $user->publisherProfile?->allowed_countries ? implode(', ', $user->publisherProfile->allowed_countries) : '' }}"
                    placeholder="e.g. ID, PK, US — leave empty to allow all">
                <div style="font-size:11px;color:#9ca3af;">Only clicks from these countries will be accepted. Leave blank to allow all countries.</div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Fraud Settings</button>
    </form>
</div>

<!-- Tracking Links -->
<div class="card">
    <div class="flex-between mb-4">
        <div class="card-title">Tracking Links</div>
        <a href="{{ route('admin.tracking.create') }}" class="btn btn-primary btn-sm">+ Add Link</a>
    </div>
    @forelse($user->trackingLinks as $link)
    <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6;">
        <div style="flex:1;">
            <div style="font-size:13px;font-weight:600;">{{ $link->name ?: 'Unnamed Link' }}</div>
            <div style="font-size:11px;color:#9ca3af;font-family:monospace;">{{ $link->tracking_url }}</div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:16px;font-weight:700;">{{ number_format($link->unique_clicks) }}</div>
            <div style="font-size:11px;color:#6b7280;">Unique</div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:16px;font-weight:700;color:#ef4444;">{{ number_format($link->fraud_clicks) }}</div>
            <div style="font-size:11px;color:#6b7280;">Fraud</div>
        </div>
        <span class="badge {{ $link->is_active ? 'badge-success' : 'badge-danger' }}">{{ $link->is_active ? 'Active' : 'Inactive' }}</span>
    </div>
    @empty
    <div style="text-align:center;padding:24px;color:#9ca3af;">No tracking links yet</div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
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
