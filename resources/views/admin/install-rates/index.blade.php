@extends('layouts.admin')
@section('title', 'Install Country Rates')
@section('page-title', 'Install Country Rates')

@section('content')

{{-- Delete forms (outside main form so they don't nest) --}}
@foreach($rates as $rate)
<form id="del-ir-{{ $rate->id }}" method="POST" action="{{ route('admin.install-rates.destroy', $rate) }}" style="display:none;">
    @csrf @method('DELETE')
</form>
@endforeach

{{-- Bulk-apply sticky bar --}}
<div id="bulkBar" style="display:none;position:sticky;top:64px;z-index:100;background:#1e293b;color:#fff;padding:12px 20px;border-radius:12px;margin-bottom:16px;display:none;align-items:center;gap:14px;flex-wrap:wrap;box-shadow:0 4px 20px rgba(0,0,0,.25);">
    <span style="font-size:13px;font-weight:600;"><span id="selCount">0</span> countries selected</span>
    <div style="display:flex;align-items:center;gap:8px;margin-left:auto;">
        <span style="font-size:13px;opacity:.7;">Apply same rate:</span>
        <span style="color:#9ca3af;font-size:13px;">$</span>
        <input type="number" id="bulkRateVal" step="0.000001" min="0" placeholder="0.000000"
               style="width:120px;padding:6px 10px;border-radius:8px;border:none;background:#334155;color:#fff;font-size:13px;font-family:inherit;outline:none;">
        <button onclick="applyBulkRate()" type="button"
                style="padding:7px 16px;background:#01BF63;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
            Apply to Selected
        </button>
        <button onclick="clearSelection()" type="button"
                style="padding:7px 12px;background:transparent;color:#9ca3af;border:1px solid #475569;border-radius:8px;font-size:12px;cursor:pointer;">
            Clear
        </button>
    </div>
</div>

<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <p style="color:var(--text-muted);font-size:14px;margin:0;">
        Set per-install rates paid to publishers on <strong>Installs Based</strong> contracts. Edit rates inline, then click <strong>Save All Changes</strong>.
    </p>
    @if($unsynced->count())
    <form method="POST" action="{{ route('admin.install-rates.sync') }}">
        @csrf
        <button type="submit" style="padding:9px 16px;background:#3b82f6;color:white;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Import {{ $unsynced->count() }} Tracked Countries (rate $0)
        </button>
    </form>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;" class="rates-layout">

    {{-- Rates Table --}}
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="font-size:15px;font-weight:700;">
                    Install Rates
                    <span style="font-size:13px;font-weight:400;color:var(--text-muted);margin-left:8px;">{{ $rates->count() }} countries</span>
                </div>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#6b7280;user-select:none;">
                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)" style="width:15px;height:15px;cursor:pointer;">
                    Select All
                </label>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <input type="text" id="rateSearch" placeholder="Search country..."
                       style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;width:180px;font-family:inherit;outline:none;">
                <button type="submit" form="bulkForm"
                        style="padding:9px 18px;background:#01BF63;color:white;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save All Changes
                </button>
            </div>
        </div>

        @if($rates->count())
        <form id="bulkForm" method="POST" action="{{ route('admin.install-rates.bulk-update') }}">
            @csrf
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th>Country</th>
                            <th>Code</th>
                            <th>Rate / Install</th>
                            <th>Active</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="ratesTableBody">
                        @foreach($rates as $rate)
                        <tr class="rate-row" data-name="{{ strtolower($rate->country_name) }}" data-code="{{ strtolower($rate->country_code) }}">
                            <td>
                                <input type="checkbox" class="row-check" data-id="{{ $rate->id }}"
                                       onchange="onRowCheck()" style="width:15px;height:15px;cursor:pointer;">
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <img src="https://flagcdn.com/24x18/{{ strtolower($rate->country_code) }}.png"
                                         width="24" height="18" style="border-radius:3px;border:1px solid #e5e7eb;flex-shrink:0;"
                                         onerror="this.style.display='none'">
                                    <span style="font-weight:500;">{{ $rate->country_name }}</span>
                                </div>
                            </td>
                            <td><code style="font-size:12px;background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ strtoupper($rate->country_code) }}</code></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:13px;color:#6b7280;">$</span>
                                    <input type="number" name="rates[{{ $rate->id }}][rate_usd]"
                                           value="{{ $rate->rate_usd }}" step="0.000001" min="0"
                                           class="rate-input"
                                           style="width:110px;padding:6px 8px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:inherit;outline:none;transition:border-color .15s;"
                                           onfocus="this.style.borderColor='#01BF63'" onblur="this.style.borderColor='#e5e7eb'">
                                </div>
                            </td>
                            <td>
                                <label style="display:flex;align-items:center;gap:7px;cursor:pointer;">
                                    <input type="checkbox" name="rates[{{ $rate->id }}][is_active]" value="1"
                                           {{ $rate->is_active ? 'checked' : '' }}
                                           style="width:15px;height:15px;cursor:pointer;accent-color:#01BF63;">
                                    <span style="font-size:12px;font-weight:600;color:{{ $rate->is_active ? '#065f46' : '#9ca3af' }};">
                                        {{ $rate->is_active ? 'Active' : 'Off' }}
                                    </span>
                                </label>
                            </td>
                            <td>
                                <button type="button"
                                        onclick="if(confirm('Delete {{ addslashes($rate->country_name) }}?')) document.getElementById('del-ir-{{ $rate->id }}').submit();"
                                        style="padding:4px 10px;background:#fee2e2;color:#991b1b;border:none;border-radius:6px;font-size:12px;cursor:pointer;font-weight:600;">
                                    Del
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        @else
        <div style="text-align:center;padding:40px;color:var(--text-muted);">No install rates configured yet. Add a country rate on the right.</div>
        @endif
    </div>

    {{-- Add Rate Form --}}
    <div>
        <div class="card" style="position:sticky;top:80px;">
            <div style="font-size:15px;font-weight:700;margin-bottom:20px;">Add Install Rate</div>
            <form method="POST" action="{{ route('admin.install-rates.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Country Code <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="country_code" class="form-control" value="{{ old('country_code') }}"
                           placeholder="US" maxlength="2" style="text-transform:uppercase;" required>
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">ISO 2-letter code (e.g. US, PK, IN)</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Country Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="country_name" class="form-control" value="{{ old('country_name') }}"
                           placeholder="United States" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Rate per Install (USD) <span style="color:#ef4444;">*</span></label>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:14px;color:#6b7280;font-weight:600;">$</span>
                        <input type="number" name="rate_usd" class="form-control" value="{{ old('rate_usd', '0') }}"
                               step="0.000001" min="0" placeholder="0.050000" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Add Rate</button>
            </form>

            @if($errors->any())
            <div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:13px;margin-top:16px;">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
            @endif
        </div>

        {{-- Info box --}}
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;margin-top:16px;font-size:13px;color:#1e40af;line-height:1.6;">
            <strong>Bulk editing tips:</strong><br>
            • Edit any rate inputs in the table, then click <strong>Save All Changes</strong> to save everything at once.<br>
            • Check multiple countries and use <strong>Apply to Selected</strong> in the top bar to set the same rate for all of them at once.
        </div>
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
    // Sync selectAll state
    const all = document.querySelectorAll('.row-check');
    document.getElementById('selectAll').indeterminate = checked.length > 0 && checked.length < all.length;
    document.getElementById('selectAll').checked = checked.length === all.length;
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
        if (row) row.querySelector('.rate-input').value = val;
    });
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    document.getElementById('selectAll').checked = false;
    onRowCheck();
}

document.getElementById('rateSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.rate-row').forEach(row => {
        const match = row.dataset.name.includes(q) || row.dataset.code.includes(q);
        row.style.display = match ? '' : 'none';
    });
});

// Highlight edited inputs
document.querySelectorAll('.rate-input').forEach(input => {
    const orig = input.value;
    input.addEventListener('input', function() {
        this.style.borderColor = this.value !== orig ? '#f59e0b' : '#e5e7eb';
        this.style.background  = this.value !== orig ? '#fffbeb' : '';
    });
});
</script>
@endpush

@push('styles')
<style>
@media(max-width:900px){ .rates-layout { grid-template-columns:1fr !important; } }
#bulkBar { display: none; }
</style>
@endpush
