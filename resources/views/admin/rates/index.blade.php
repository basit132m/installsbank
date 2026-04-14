@extends('layouts.admin')
@section('title', 'Country Rates')
@section('page-title', 'Country Click Rates')

@section('content')

{{-- Delete forms (outside main form) --}}
@foreach($rates as $rate)
<form id="del-cr-{{ $rate->id }}" method="POST" action="{{ route('admin.rates.destroy', $rate) }}" style="display:none;">
    @csrf @method('DELETE')
</form>
@endforeach

{{-- Bulk-apply sticky bar --}}
<div id="bulkBar" style="display:none;position:sticky;top:64px;z-index:100;background:#1e293b;color:#fff;padding:12px 20px;border-radius:12px;margin-bottom:16px;align-items:center;gap:14px;flex-wrap:wrap;box-shadow:0 4px 20px rgba(0,0,0,.25);">
    <span style="font-size:13px;font-weight:600;"><span id="selCount">0</span> countries selected</span>
    <div style="display:flex;align-items:center;gap:8px;margin-left:auto;">
        <span style="font-size:13px;opacity:.7;">Apply same rate:</span>
        <span style="color:#9ca3af;font-size:13px;">$</span>
        <input type="number" id="bulkRateVal" step="0.000001" min="0" placeholder="0.000000"
               style="width:130px;padding:6px 10px;border-radius:8px;border:none;background:#334155;color:#fff;font-size:13px;font-family:inherit;outline:none;">
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

{{-- Unrated countries alert --}}
@if($unratedCount > 0)
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:14px;">
    <div style="width:40px;height:40px;background:#f59e0b;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:14px;font-weight:700;color:#92400e;">{{ $unratedCount }} {{ Str::plural('country', $unratedCount) }} detected with no rate set</div>
        <div style="font-size:13px;color:#b45309;margin-top:2px;">These countries were auto-detected from incoming clicks. Clicks are tracked but earnings show <strong>N/A</strong> until you set a rate. They are highlighted below.</div>
    </div>
</div>
@endif

<div class="grid-2 mb-6" style="align-items:start;">
    <!-- Add Rate -->
    <div class="card" style="position:sticky;top:80px;">
        <div class="card-title mb-4">Add Country Rate</div>
        <form method="POST" action="{{ route('admin.rates.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Country Code</label>
                <input type="text" name="country_code" class="form-control" placeholder="US" maxlength="5" required style="text-transform:uppercase;">
            </div>
            <div class="form-group">
                <label class="form-label">Country Name</label>
                <input type="text" name="country_name" class="form-control" placeholder="United States" required>
            </div>
            <div class="form-group">
                <label class="form-label">Rate Per Click (USD)</label>
                <input type="number" name="rate_per_click" class="form-control" placeholder="0.000500" step="0.000001" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Rate</button>
        </form>

        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;margin-top:16px;font-size:13px;color:#1e40af;line-height:1.6;">
            <strong>Bulk editing tips:</strong><br>
            • Edit any rate inputs in the table, then click <strong>Save All Changes</strong>.<br>
            • Check multiple countries and use <strong>Apply to Selected</strong> in the bar above to set the same rate for all at once.
        </div>
    </div>

    <!-- Rates Table -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="card-title">All Country Rates</div>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#6b7280;user-select:none;">
                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)" style="width:15px;height:15px;cursor:pointer;">
                    Select All
                </label>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                @if($unratedCount > 0)
                    <span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ $unratedCount }} need rate</span>
                @endif
                <span style="font-size:13px;color:#9ca3af;">{{ $rates->total() }} countries</span>
                <button type="submit" form="bulkForm"
                        style="padding:8px 16px;background:#01BF63;color:white;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save All Changes
                </button>
            </div>
        </div>

        <form id="bulkForm" method="POST" action="{{ route('admin.rates.bulk-update') }}">
            @csrf
            <div class="table-wrap" style="max-height:600px;overflow-y:auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th>Country</th>
                            <th>Code</th>
                            <th>Rate / Click</th>
                            <th>Active</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                        <tr style="{{ $rate->needs_rate_update ? 'background:#fffbeb;' : '' }}">
                            <td>
                                <input type="checkbox" class="row-check" data-id="{{ $rate->id }}"
                                       onchange="onRowCheck()" style="width:15px;height:15px;cursor:pointer;">
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($rate->needs_rate_update)
                                        <span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                                    @endif
                                    <div>
                                        <div style="font-size:13px;font-weight:{{ $rate->needs_rate_update ? '700' : '400' }};">{{ $rate->country_name }}</div>
                                        @if($rate->needs_rate_update)
                                            <div style="font-size:11px;color:#d97706;">Auto-detected · Rate needed</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $rate->country_code }}</code></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:12px;color:#6b7280;">$</span>
                                    <input type="number" name="rates[{{ $rate->id }}][rate_per_click]"
                                           value="{{ $rate->needs_rate_update ? '' : $rate->rate_per_click }}"
                                           step="0.000001" min="0" required
                                           placeholder="{{ $rate->needs_rate_update ? 'Set rate...' : '0.000000' }}"
                                           class="rate-input"
                                           style="width:110px;padding:5px 8px;border:{{ $rate->needs_rate_update ? '1.5px solid #f59e0b' : '1.5px solid #e5e7eb' }};border-radius:8px;font-size:12px;font-family:inherit;background:{{ $rate->needs_rate_update ? '#fffbeb' : 'white' }};outline:none;">
                                </div>
                            </td>
                            <td>
                                @if($rate->needs_rate_update)
                                    <span class="badge badge-warning">N/A</span>
                                @else
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                        <input type="checkbox" name="rates[{{ $rate->id }}][is_active]" value="1"
                                               {{ $rate->is_active ? 'checked' : '' }}
                                               style="width:15px;height:15px;cursor:pointer;accent-color:#01BF63;">
                                        <span style="font-size:12px;font-weight:600;color:{{ $rate->is_active ? '#065f46' : '#9ca3af' }};">
                                            {{ $rate->is_active ? 'On' : 'Off' }}
                                        </span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                        onclick="if(confirm('Delete {{ addslashes($rate->country_name) }}?')) document.getElementById('del-cr-{{ $rate->id }}').submit();"
                                        style="padding:4px 8px;background:#fee2e2;color:#991b1b;border:none;border-radius:6px;font-size:11px;cursor:pointer;font-weight:600;">
                                    Del
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:24px;color:#9ca3af;">No rates configured</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div style="margin-top:12px;">{{ $rates->links() }}</div>
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
