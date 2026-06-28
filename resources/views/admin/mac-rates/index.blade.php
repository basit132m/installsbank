@extends('layouts.admin')
@section('title', 'Mac Country Click Rates')
@section('page-title', 'Mac Country Click Rates')

@section('content')

{{-- Delete forms (hidden) --}}
@foreach($rates as $rate)
<form id="del-mr-{{ $rate->id }}" method="POST" action="{{ route('admin.mac-rates.destroy', $rate) }}" style="display:none;">
    @csrf @method('DELETE')
</form>
@endforeach

{{-- HERO STRIP --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:20px;padding:24px 32px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;right:-50px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(139,92,246,.15) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-30px;left:25%;width:150px;height:150px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,.12) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;position:relative;">
        <div style="flex:1;min-width:180px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#64748b;margin-bottom:6px;">Per-Click Revenue · Mac</div>
            <div style="font-size:24px;font-weight:900;color:#f8fafc;letter-spacing:-0.5px;line-height:1.1;">Mac Country Click Rates</div>
            <div style="font-size:13px;color:#64748b;margin-top:4px;">Set how much publishers earn per valid Mac click by country</div>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;flex-shrink:0;">
            <div style="background:rgba(139,92,246,.12);border:1px solid rgba(139,92,246,.25);border-radius:14px;padding:12px 20px;text-align:center;min-width:100px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#c4b5fd;margin-bottom:3px;">Total</div>
                <div style="font-size:24px;font-weight:900;color:#8b5cf6;line-height:1;">{{ $rates->total() }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">countries</div>
            </div>
            @if($unratedCount > 0)
            <div style="background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.25);border-radius:14px;padding:12px 20px;text-align:center;min-width:100px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#fcd34d;margin-bottom:3px;">Need Rate</div>
                <div style="font-size:24px;font-weight:900;color:#f59e0b;line-height:1;">{{ $unratedCount }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">unrated</div>
            </div>
            @endif
            <div style="background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.25);border-radius:14px;padding:12px 20px;text-align:center;min-width:100px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#93c5fd;margin-bottom:3px;">Active</div>
                <div style="font-size:24px;font-weight:900;color:#60a5fa;line-height:1;">{{ $rates->where('is_active', true)->count() }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px;">earning</div>
            </div>
        </div>
    </div>
</div>

{{-- BULK APPLY STICKY BAR --}}
<div id="bulkBar" style="display:none;position:sticky;top:64px;z-index:100;background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;padding:14px 22px;border-radius:14px;margin-bottom:18px;align-items:center;gap:14px;flex-wrap:wrap;box-shadow:0 8px 32px rgba(0,0,0,.35);border:1px solid rgba(255,255,255,.08);">
    <div style="display:flex;align-items:center;gap:8px;">
        <div style="width:28px;height:28px;background:rgba(139,92,246,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;">
            <svg width="14" height="14" fill="none" stroke="#8b5cf6" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span style="font-size:13px;font-weight:700;"><span id="selCount">0</span> countries selected</span>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-left:auto;flex-wrap:wrap;">
        <span style="font-size:13px;color:#64748b;">Apply same rate:</span>
        <div style="display:flex;align-items:center;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:9px;padding:0 10px;">
            <span style="color:#64748b;font-size:13px;margin-right:4px;">$</span>
            <input type="number" id="bulkRateVal" step="0.000001" min="0" placeholder="0.000000"
                   style="width:120px;padding:7px 0;border:none;background:transparent;color:#fff;font-size:13px;font-family:inherit;outline:none;">
        </div>
        <button onclick="applyBulkRate()" type="button"
                style="padding:8px 18px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(139,92,246,.4);">
            Apply to Selected
        </button>
        <button onclick="clearSelection()" type="button"
                style="padding:8px 12px;background:transparent;color:#94a3b8;border:1px solid #334155;border-radius:9px;font-size:12px;cursor:pointer;">
            Clear
        </button>
    </div>
</div>

{{-- UNRATED ALERT --}}
@if($unratedCount > 0)
<div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:16px;">
    <div style="width:42px;height:42px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(245,158,11,.3);">
        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div>
        <div style="font-size:14px;font-weight:800;color:#92400e;">{{ $unratedCount }} {{ Str::plural('country', $unratedCount) }} detected with no Mac rate set</div>
        <div style="font-size:13px;color:#b45309;margin-top:3px;">Auto-detected from incoming Mac clicks. Clicks are tracked but earnings show <strong>N/A</strong> until you set a rate.</div>
    </div>
</div>
@endif

@if(session('success'))
<div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;display:flex;align-items:center;gap:10px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- MAIN GRID: Add form (left) + Table (right) --}}
<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;">

    {{-- ADD RATE CARD --}}
    <div style="position:sticky;top:80px;">
        <div class="card" style="border:1.5px solid #e5e7eb;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                <div style="width:40px;height:40px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(139,92,246,.25);">
                    <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:#111827;">Add Mac Country Rate</div>
                    <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Set per-Mac-click earnings</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.mac-rates.store') }}">
                @csrf
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Country Code</label>
                    <input type="text" name="country_code" class="form-control" placeholder="US, PK, ID…" maxlength="5" required style="text-transform:uppercase;font-family:monospace;font-weight:700;letter-spacing:0.08em;">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Country Name</label>
                    <input type="text" name="country_name" class="form-control" placeholder="United States" required>
                </div>
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Mac Rate Per Click (USD)</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px;color:#9ca3af;font-weight:600;">$</span>
                        <input type="number" name="mac_rate_per_click" class="form-control" placeholder="0.000500" step="0.000001" min="0" required style="padding-left:26px;font-family:monospace;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border:none;box-shadow:0 4px 12px rgba(139,92,246,.3);">
                    Add Mac Country Rate
                </button>
            </form>

            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;margin-top:16px;font-size:12px;color:#1e40af;line-height:1.7;">
                <strong style="display:block;margin-bottom:4px;">Bulk editing tips</strong>
                • Edit rate inputs in the table, click <strong>Save All Changes</strong>.<br>
                • Check multiple rows, then use <strong>Apply to Selected</strong> in the bar above to set the same rate at once.
            </div>
        </div>
    </div>

    {{-- RATES TABLE CARD --}}
    <div class="card" style="padding:0;overflow:hidden;border:1.5px solid #e5e7eb;">

        {{-- Table header --}}
        <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 10px rgba(139,92,246,.25);">
                <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size:14px;font-weight:800;color:#111827;">All Mac Country Rates</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:1px;">
                    {{ $rates->total() }} countries
                    @if($unratedCount > 0)
                    &nbsp;·&nbsp;<span style="color:#d97706;font-weight:700;">{{ $unratedCount }} need rate</span>
                    @endif
                </div>
            </div>

            <div style="margin-left:auto;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#6b7280;user-select:none;">
                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)"
                           style="width:15px;height:15px;cursor:pointer;accent-color:#8b5cf6;">
                    Select All
                </label>
                <button type="submit" form="bulkForm"
                        style="display:flex;align-items:center;gap:7px;padding:9px 18px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);color:white;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(139,92,246,.3);">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save All Changes
                </button>
            </div>
        </div>

        {{-- Table --}}
        <form id="bulkForm" method="POST" action="{{ route('admin.mac-rates.bulk-update') }}">
            @csrf
            <div style="max-height:640px;overflow-y:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                            <th style="width:40px;padding:10px 14px;"></th>
                            <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;">Country</th>
                            <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;">Code</th>
                            <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;">Mac Rate / Click</th>
                            <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;">Active</th>
                            <th style="width:60px;padding:10px 14px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                        <tr style="border-bottom:1px solid #f3f4f6;{{ $rate->needs_rate_update ? 'background:#fffef0;' : '' }}"
                            onmouseover="this.style.background='{{ $rate->needs_rate_update ? '#fffbeb' : '#fafafa' }}'"
                            onmouseout="this.style.background='{{ $rate->needs_rate_update ? '#fffef0' : '' }}'">

                            <td style="padding:12px 14px;">
                                <input type="checkbox" class="row-check" data-id="{{ $rate->id }}"
                                       onchange="onRowCheck()"
                                       style="width:15px;height:15px;cursor:pointer;accent-color:#8b5cf6;">
                            </td>

                            <td style="padding:12px 14px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <img src="https://flagcdn.com/20x15/{{ strtolower($rate->country_code) }}.png"
                                         style="border-radius:2px;flex-shrink:0;border:1px solid #e5e7eb;"
                                         onerror="this.style.display='none'">
                                    <div>
                                        <div style="font-size:13px;font-weight:{{ $rate->needs_rate_update ? '700' : '600' }};color:#111827;">
                                            {{ $rate->country_name }}
                                        </div>
                                        @if($rate->needs_rate_update)
                                        <div style="font-size:11px;color:#d97706;font-weight:600;margin-top:1px;">Auto-detected · Rate needed</div>
                                        @endif
                                    </div>
                                    @if($rate->needs_rate_update)
                                    <span style="width:7px;height:7px;background:#f59e0b;border-radius:50%;display:inline-block;flex-shrink:0;box-shadow:0 0 5px #f59e0b;"></span>
                                    @endif
                                </div>
                            </td>

                            <td style="padding:12px 14px;text-align:center;">
                                <code style="font-size:12px;font-weight:700;background:#f3f4f6;color:#374151;padding:3px 8px;border-radius:6px;letter-spacing:0.05em;">{{ $rate->country_code }}</code>
                            </td>

                            <td style="padding:12px 14px;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:13px;color:#9ca3af;font-weight:600;">$</span>
                                    <input type="number"
                                           name="rates[{{ $rate->id }}][mac_rate_per_click]"
                                           value="{{ $rate->needs_rate_update ? '' : $rate->mac_rate_per_click }}"
                                           step="0.000001" min="0" required
                                           placeholder="{{ $rate->needs_rate_update ? 'Set rate…' : '0.000000' }}"
                                           class="rate-input"
                                           style="width:120px;padding:6px 10px;border:1.5px solid {{ $rate->needs_rate_update ? '#f59e0b' : '#e5e7eb' }};border-radius:8px;font-size:12px;font-family:monospace;background:{{ $rate->needs_rate_update ? '#fffbeb' : 'white' }};color:#111827;outline:none;transition:border-color .15s;">
                                </div>
                            </td>

                            <td style="padding:12px 14px;text-align:center;">
                                @if($rate->needs_rate_update)
                                    <span style="background:#fffbeb;color:#d97706;border:1px solid #fde68a;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">N/A</span>
                                @else
                                    <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
                                        <input type="checkbox" name="rates[{{ $rate->id }}][is_active]" value="1"
                                               {{ $rate->is_active ? 'checked' : '' }}
                                               class="active-check-{{ $rate->id }}"
                                               onchange="updateActiveLabel({{ $rate->id }}, this.checked)"
                                               style="width:15px;height:15px;cursor:pointer;accent-color:#8b5cf6;">
                                        <span id="active-label-{{ $rate->id }}"
                                              style="font-size:12px;font-weight:700;color:{{ $rate->is_active ? '#059669' : '#9ca3af' }};">
                                            {{ $rate->is_active ? 'On' : 'Off' }}
                                        </span>
                                    </label>
                                @endif
                            </td>

                            <td style="padding:12px 14px;text-align:center;">
                                <button type="button"
                                        onclick="if(confirm('Delete Mac rate for {{ addslashes($rate->country_name) }}?')) document.getElementById('del-mr-{{ $rate->id }}').submit();"
                                        style="width:32px;height:32px;background:#fef2f2;color:#ef4444;border:1px solid #fecaca;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;"
                                        title="Delete">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px 24px;color:#9ca3af;">
                                <svg width="36" height="36" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945"/></svg>
                                <div style="font-size:14px;font-weight:600;">No Mac rates configured yet</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if($rates->hasPages())
        <div style="padding:14px 20px;border-top:1px solid #f3f4f6;">
            {{ $rates->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function onRowCheck() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar     = document.getElementById('bulkBar');
    document.getElementById('selCount').textContent = checked.length;
    bar.style.display = checked.length > 0 ? 'flex' : 'none';
    const all = document.querySelectorAll('.row-check');
    document.getElementById('selectAll').indeterminate = checked.length > 0 && checked.length < all.length;
    document.getElementById('selectAll').checked = checked.length === all.length && all.length > 0;
}

function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(c => c.checked = master.checked);
    onRowCheck();
}

function applyBulkRate() {
    const val = document.getElementById('bulkRateVal').value;
    if (val === '') { alert('Enter a rate value first.'); return; }
    document.querySelectorAll('.row-check:checked').forEach(cb => {
        const row = cb.closest('tr');
        if (row) {
            const input = row.querySelector('.rate-input');
            if (input) {
                input.value = val;
                input.style.borderColor = '#f59e0b';
                input.style.background  = '#fffbeb';
            }
        }
    });
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    document.getElementById('selectAll').checked = false;
    onRowCheck();
}

function updateActiveLabel(id, checked) {
    const label = document.getElementById('active-label-' + id);
    if (label) {
        label.textContent = checked ? 'On' : 'Off';
        label.style.color = checked ? '#059669' : '#9ca3af';
    }
}

document.querySelectorAll('.rate-input').forEach(input => {
    const orig = input.value;
    input.addEventListener('input', function() {
        if (this.value !== orig) {
            this.style.borderColor = '#f59e0b';
            this.style.background  = '#fffbeb';
        } else {
            this.style.borderColor = '#e5e7eb';
            this.style.background  = '';
        }
    });
    input.addEventListener('focus', function() {
        this.style.borderColor = '#8b5cf6';
        this.style.boxShadow   = '0 0 0 3px rgba(139,92,246,.15)';
    });
    input.addEventListener('blur', function() {
        this.style.boxShadow = '';
        if (this.value === orig) this.style.borderColor = '#e5e7eb';
    });
});
</script>
@endpush
