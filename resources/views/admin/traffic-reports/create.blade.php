@extends('layouts.admin')
@section('title', 'Traffic Reports')
@section('page-title', 'Traffic Testing Reports')

@section('content')
<div style="max-width:720px;">
    <a href="{{ route('admin.traffic-reports.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:16px;">← Saved Reports</a>


    <div style="background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:16px;padding:24px 28px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="none" stroke="#38bdf8" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div style="font-size:17px;font-weight:800;color:#fff;">Generate a Traffic Report</div>
                <div style="font-size:12px;color:#94a3b8;margin-top:3px;">Pick a date range, fetch the numbers, adjust the clicks if needed, then export a clean PDF with your logo &amp; stamp.</div>
            </div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.traffic-reports.fetch') }}">
            @csrf

            <input type="hidden" name="range_mode" id="rangeMode" value="custom">

            {{-- Rolling-window presets --}}
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                @foreach(['24h'=>'Last 24 Hours','24h_prev'=>'Previous 24 Hours','48h'=>'Last 48 Hours'] as $key => $lbl)
                    <button type="button" class="preset-btn roll-btn" data-preset="{{ $key }}" data-rlabel="{{ $lbl }}"
                            style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;border:1.5px solid #ddd6fe;background:#f5f3ff;color:#6d28d9;cursor:pointer;">⏱ {{ $lbl }}</button>
                @endforeach
            </div>
            <div style="font-size:11px;color:#9ca3af;margin-bottom:8px;">Generate the last 24h and the previous 24h as two separate reports if you want to split a 48-hour span.</div>

            {{-- Quick date presets --}}
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;">
                @foreach(['today'=>'Today','yesterday'=>'Yesterday','7days'=>'Last 7 Days','30days'=>'Last 30 Days','this_month'=>'This Month','last_month'=>'Last Month'] as $key => $lbl)
                    <button type="button" class="preset-btn" data-preset="{{ $key }}"
                            style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;cursor:pointer;">{{ $lbl }}</button>
                @endforeach
            </div>

            <div id="rollNote" style="display:none;background:#f5f3ff;border:1.5px solid #ddd6fe;border-radius:8px;padding:10px 14px;margin-bottom:12px;font-size:12px;color:#6d28d9;">
                Using a <strong id="rollLabel"></strong> rolling window (based on the exact current time). The date fields below are ignored.
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">From Date *</label>
                    <input type="date" id="dateFrom" name="date_from" class="form-control" value="{{ old('date_from', now()->subDays(6)->toDateString()) }}" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">To Date *</label>
                    <input type="date" id="dateTo" name="date_to" class="form-control" value="{{ old('date_to', now()->toDateString()) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Traffic Source</label>
                <select name="user_id" class="form-control form-select">
                    <option value="">All Traffic (every publisher &amp; reseller)</option>
                    <optgroup label="Publishers &amp; Resellers">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} — {{ ucfirst($u->role) }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Leave as "All Traffic" for a platform-wide report, or pick a specific account (covers all of that account's links).</div>
            </div>

            <div class="form-group">
                <label class="form-label">Or a Specific Tracking Link</label>
                <select name="tracking_link_id" class="form-control form-select">
                    <option value="">— Use the traffic source above —</option>
                    @foreach($links as $l)
                        <option value="{{ $l['id'] }}" {{ old('tracking_link_id') == $l['id'] ? 'selected' : '' }}>{{ $l['label'] }}</option>
                    @endforeach
                </select>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Pick a single link to report on just that tracking code. <strong>If set, this overrides the traffic source above.</strong></div>
            </div>

            <div class="form-group">
                <label class="form-label">Click Type</label>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <label style="flex:1;min-width:200px;display:flex;gap:10px;align-items:flex-start;border:1.5px solid #e5e7eb;border-radius:10px;padding:12px 14px;cursor:pointer;">
                        <input type="radio" name="click_type" value="valid" checked style="margin-top:3px;">
                        <span><strong style="font-size:13px;">Valid Clicks</strong><br><span style="font-size:12px;color:#9ca3af;">Counted, non-fraud traffic (recommended)</span></span>
                    </label>
                    <label style="flex:1;min-width:200px;display:flex;gap:10px;align-items:flex-start;border:1.5px solid #e5e7eb;border-radius:10px;padding:12px 14px;cursor:pointer;">
                        <input type="radio" name="click_type" value="all" style="margin-top:3px;">
                        <span><strong style="font-size:13px;">All Clicks</strong><br><span style="font-size:12px;color:#9ca3af;">Every recorded click incl. fraud</span></span>
                    </label>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Report Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', 'Traffic Testing Report') }}" placeholder="Traffic Testing Report">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Prepared For (optional)</label>
                    <input type="text" name="prepared_for" class="form-control" value="{{ old('prepared_for') }}" placeholder="Client / Advertiser name">
                </div>
            </div>

            <div class="form-group" style="border:1.5px solid #e5e7eb;border-radius:10px;padding:12px 14px;margin-bottom:20px;">
                <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer;margin:0;">
                    <input type="checkbox" name="show_branding" value="1" {{ old('show_branding', '1') ? 'checked' : '' }} style="margin-top:3px;">
                    <span>
                        <strong style="font-size:13px;">Show Installs Bank branding</strong><br>
                        <span style="font-size:12px;color:#9ca3af;">Includes the logo, the "Installs Bank" name and installsbank.com on the report. Uncheck for a fully unbranded report.</span>
                    </span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Fetch Data →</button>
        </form>
    </div>
</div>

<script>
(function () {
    const fromEl = document.getElementById('dateFrom');
    const toEl   = document.getElementById('dateTo');
    const iso    = d => d.toISOString().slice(0, 10);

    function range(preset) {
        const now = new Date();
        const t   = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // today, midnight local
        let from = new Date(t), to = new Date(t);
        switch (preset) {
            case 'today':      break;
            case 'yesterday':  from.setDate(t.getDate() - 1); to.setDate(t.getDate() - 1); break;
            case '7days':      from.setDate(t.getDate() - 6); break;
            case '30days':     from.setDate(t.getDate() - 29); break;
            case 'this_month': from = new Date(t.getFullYear(), t.getMonth(), 1); break;
            case 'last_month':
                from = new Date(t.getFullYear(), t.getMonth() - 1, 1);
                to   = new Date(t.getFullYear(), t.getMonth(), 0);
                break;
        }
        return [iso(from), iso(to)];
    }

    const modeEl  = document.getElementById('rangeMode');
    const rollNote = document.getElementById('rollNote');

    function clearActive() {
        document.querySelectorAll('.preset-btn').forEach(b => {
            if (b.classList.contains('roll-btn')) { b.style.background = '#f5f3ff'; b.style.color = '#6d28d9'; b.style.borderColor = '#ddd6fe'; }
            else { b.style.background = '#fff'; b.style.color = '#6b7280'; b.style.borderColor = '#e5e7eb'; }
        });
    }

    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const preset = btn.dataset.preset;
            clearActive();
            btn.style.background = btn.classList.contains('roll-btn') ? '#7c3aed' : '#01BF63';
            btn.style.color = '#fff';
            btn.style.borderColor = btn.style.background;

            if (btn.classList.contains('roll-btn')) {
                // Rolling window — dates are just placeholders (today) so validation passes
                modeEl.value = preset;
                const t = iso(new Date());
                fromEl.value = t; toEl.value = t;
                document.getElementById('rollLabel').textContent = btn.dataset.rlabel || 'rolling';
                rollNote.style.display = 'block';
            } else {
                modeEl.value = 'custom';
                rollNote.style.display = 'none';
                const [f, tt] = range(preset);
                fromEl.value = f; toEl.value = tt;
            }
        });
    });

    // Editing dates manually switches back to a custom calendar range
    [fromEl, toEl].forEach(el => el.addEventListener('input', () => {
        modeEl.value = 'custom';
        rollNote.style.display = 'none';
        clearActive();
    }));
})();
</script>
@endsection
