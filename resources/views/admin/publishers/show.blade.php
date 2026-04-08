@extends('layouts.admin')
@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
<div style="display:flex;gap:8px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('admin.publishers.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    <a href="{{ route('admin.publishers.stats', $user) }}" class="btn btn-ghost btn-sm" style="color:#3b82f6;border-color:#3b82f6;">📊 Detailed Stats</a>
    <span class="badge {{ $user->status === 'active' ? 'badge-success' : ($user->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">{{ ucfirst($user->status) }}</span>
    @if($user->status === 'pending')
        <form method="POST" action="{{ route('admin.publishers.activate', $user) }}" style="display:inline;">@csrf<button class="btn btn-success btn-sm">Activate Publisher</button></form>
    @elseif($user->status === 'active')
        <form method="POST" action="{{ route('admin.publishers.suspend', $user) }}" style="display:inline;" onsubmit="return confirm('Suspend this publisher?')">@csrf<button class="btn btn-danger btn-sm">Suspend</button></form>
    @endif
    <form method="POST" action="{{ route('admin.publishers.destroy', $user) }}" style="display:inline;margin-left:auto;"
          onsubmit="return confirm('DELETE {{ addslashes($user->name) }}?\n\nThis will permanently delete the publisher and ALL their data including clicks, earnings, tracking links, withdrawals, and fraud alerts.\n\nThis cannot be undone.')">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-sm">🗑 Delete Publisher</button>
    </form>
</div>

<!-- Publisher Info + Quick Stats -->
<div class="grid-2 mb-6">
    <div class="card">
        <div class="card-title mb-4">Publisher Information</div>
        <table style="width:100%;">
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;width:40%;">Email</td><td style="font-size:14px;">{{ $user->email }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Phone</td><td style="font-size:14px;">{{ $user->phone ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Telegram</td><td style="font-size:14px;">{{ $user->telegram ?? '—' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Website</td><td style="font-size:14px;"><a href="{{ $user->website }}" target="_blank" style="color:#01BF63;">{{ $user->website ?? '—' }}</a></td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Registered</td><td style="font-size:14px;">{{ $user->created_at->format('M d, Y H:i') }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Last Login</td><td style="font-size:14px;">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Contract</td><td><span class="badge {{ $user->publisherProfile?->contract_type !== 'none' ? 'badge-primary' : 'badge-gray' }}">{{ ucfirst($user->publisherProfile?->contract_type ?? 'none') }}</span></td></tr>
            <tr><td style="color:#6b7280;font-size:13px;padding:6px 0;">Payment Enabled</td><td><span class="badge {{ $user->publisherProfile?->payment_enabled ? 'badge-success' : 'badge-gray' }}">{{ $user->publisherProfile?->payment_enabled ? 'Yes' : 'No' }}</span></td></tr>
        </table>

        @if($user->stat_screenshots && count($user->stat_screenshots) > 0)
        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:10px;">
                📊 Submitted Statistics Screenshots
                <span style="background:#e6faf2;color:#065f46;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;margin-left:6px;">{{ count($user->stat_screenshots) }} file(s)</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:8px;">
                @foreach($user->stat_screenshots as $i => $path)
                <a href="{{ asset('storage/' . $path) }}" target="_blank"
                   style="display:block;border-radius:8px;overflow:hidden;border:1.5px solid #e5e7eb;transition:border-color 0.15s;position:relative;"
                   onmouseover="this.style.borderColor='#01BF63'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <img src="{{ asset('storage/' . $path) }}" alt="Screenshot {{ $i+1 }}"
                         style="width:100%;height:90px;object-fit:cover;display:block;">
                    <div style="background:#f9fafb;padding:4px 8px;font-size:11px;color:#6b7280;font-weight:600;">
                        Screenshot {{ $i+1 }} — click to view full
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @else
        <div style="margin-top:16px;padding:10px 14px;background:#fff7ed;border-radius:8px;font-size:12px;color:#92400e;">
            ⚠️ No statistics screenshots submitted with this application.
        </div>
        @endif
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

<!-- Publisher Websites -->
@php $publisherWebsites = $user->publisherWebsites ?? \App\Models\PublisherWebsite::where('user_id',$user->id)->with('trackingLink')->latest()->get(); @endphp
<div class="card mt-6">
    <div class="flex-between mb-4">
        <div>
            <div class="card-title">Submitted Websites</div>
            <div class="card-subtitle">Websites this publisher has registered for separate ad codes</div>
        </div>
        <a href="{{ route('admin.publisher-websites.index') }}" class="btn btn-ghost btn-sm">View All Requests →</a>
    </div>
    @forelse($publisherWebsites as $website)
    <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f3f4f6;flex-wrap:wrap;">
        <div style="flex:1;min-width:180px;">
            <div style="font-size:13px;font-weight:600;">{{ $website->domain }}</div>
            <div style="font-size:11px;color:#9ca3af;">Submitted {{ $website->created_at->format('M d, Y') }}</div>
        </div>
        <span class="badge {{ $website->status === 'approved' ? 'badge-success' : ($website->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
            {{ ucfirst($website->status) }}
        </span>
        @if($website->trackingLink)
        <div style="font-size:11px;font-family:monospace;color:#6b7280;">{{ $website->trackingLink->unique_clicks }} valid clicks</div>
        @endif
        @if($website->isPending())
        <a href="{{ route('admin.publisher-websites.index') }}" class="btn btn-primary btn-sm">Review</a>
        @endif
    </div>
    @empty
    <div style="text-align:center;padding:24px;color:#9ca3af;">No website submissions yet</div>
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
