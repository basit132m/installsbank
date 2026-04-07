@extends('layouts.admin')
@section('title', 'Install Country Rates')
@section('page-title', 'Install Country Rates')

@section('content')
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <p style="color:var(--text-muted);font-size:14px;margin:0;">
        Set per-install rates paid to publishers on <strong>Installs Based</strong> contracts. Countries are discovered from tracked click data.
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
            <div style="font-size:15px;font-weight:700;">
                Install Rates
                <span style="font-size:13px;font-weight:400;color:var(--text-muted);margin-left:8px;">{{ $rates->count() }} countries</span>
            </div>
            <div style="display:flex;gap:8px;">
                <input type="text" id="rateSearch" placeholder="Search country..." style="padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;width:180px;font-family:inherit;outline:none;">
            </div>
        </div>

        @if($rates->count())
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Code</th>
                        <th>Rate / Install</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ratesTableBody">
                    @foreach($rates as $rate)
                    <tr class="rate-row" data-name="{{ strtolower($rate->country_name) }}" data-code="{{ strtolower($rate->country_code) }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="https://flagcdn.com/24x18/{{ strtolower($rate->country_code) }}.png"
                                     width="24" height="18" style="border-radius:3px;border:1px solid #e5e7eb;"
                                     onerror="this.style.display='none'">
                                <span style="font-weight:500;">{{ $rate->country_name }}</span>
                            </div>
                        </td>
                        <td><code style="font-size:12px;background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ strtoupper($rate->country_code) }}</code></td>
                        <td>
                            <form method="POST" action="{{ route('admin.install-rates.update', $rate) }}" style="display:flex;gap:6px;align-items:center;">
                                @csrf @method('PUT')
                                <span style="font-size:13px;color:#6b7280;">$</span>
                                <input type="number" name="rate_usd" value="{{ $rate->rate_usd }}" step="0.000001" min="0"
                                       style="width:100px;padding:5px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;font-family:inherit;">
                                <input type="hidden" name="is_active" value="{{ $rate->is_active ? '1' : '0' }}">
                                <button type="submit" style="padding:5px 10px;background:#3b82f6;color:white;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Save</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.install-rates.update', $rate) }}" style="display:inline;">
                                @csrf @method('PUT')
                                <input type="hidden" name="rate_usd" value="{{ $rate->rate_usd }}">
                                <input type="hidden" name="is_active" value="{{ $rate->is_active ? '0' : '1' }}">
                                <button type="submit" style="padding:4px 10px;border:none;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;background:{{ $rate->is_active ? '#d1fae5' : '#f3f4f6' }};color:{{ $rate->is_active ? '#065f46' : '#6b7280' }};">
                                    {{ $rate->is_active ? 'Active' : 'Disabled' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.install-rates.destroy', $rate) }}" onsubmit="return confirm('Delete this rate?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding:4px 10px;background:#fee2e2;color:#991b1b;border:none;border-radius:6px;font-size:12px;cursor:pointer;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
            <strong>How install rates work:</strong><br>
            Publishers on <em>Installs Based</em> contracts earn this rate per install. The number of clicks that count as one install is controlled by the <a href="{{ route('admin.install-settings.index') }}" style="color:#2563eb;font-weight:600;">Install Settings</a> (per-weekday ratio). If a rate is $0 or inactive, installs are tracked but no earnings are credited.
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('rateSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.rate-row').forEach(row => {
        const match = row.dataset.name.includes(q) || row.dataset.code.includes(q);
        row.style.display = match ? '' : 'none';
    });
});
</script>
@endpush

@push('styles')
<style>
@media(max-width:900px){ .rates-layout { grid-template-columns:1fr !important; } }
</style>
@endpush
