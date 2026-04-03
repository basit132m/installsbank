@extends('layouts.admin')
@section('title', 'Country Rates')
@section('page-title', 'Country Click Rates')

@section('content')

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
    <div class="card">
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
    </div>

    <!-- Rates Table -->
    <div class="card">
        <div class="flex-between mb-4">
            <div class="card-title">All Country Rates</div>
            <div style="display:flex;align-items:center;gap:10px;">
                @if($unratedCount > 0)
                    <span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ $unratedCount }} need rate</span>
                @endif
                <span style="font-size:13px;color:#9ca3af;">{{ $rates->total() }} countries</span>
            </div>
        </div>
        <div class="table-wrap" style="max-height:520px;overflow-y:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Code</th>
                        <th>Rate / Click</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rates as $rate)
                    <tr style="{{ $rate->needs_rate_update ? 'background:#fffbeb;' : '' }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($rate->needs_rate_update)
                                    <span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                                @endif
                                <span style="font-size:13px;font-weight:{{ $rate->needs_rate_update ? '700' : '400' }};">{{ $rate->country_name }}</span>
                            </div>
                            @if($rate->needs_rate_update)
                                <div style="font-size:11px;color:#d97706;margin-top:2px;margin-left:16px;">Auto-detected · Rate needed</div>
                            @endif
                        </td>
                        <td><code>{{ $rate->country_code }}</code></td>
                        <td>
                            <form method="POST" action="{{ route('admin.rates.update', $rate) }}" style="display:flex;gap:6px;align-items:center;">
                                @csrf @method('PUT')
                                <input type="number" name="rate_per_click"
                                    value="{{ $rate->needs_rate_update ? '' : $rate->rate_per_click }}"
                                    step="0.000001" min="0" required
                                    placeholder="{{ $rate->needs_rate_update ? 'Set rate...' : '0.000000' }}"
                                    style="width:100px;padding:4px 8px;border:{{ $rate->needs_rate_update ? '1.5px solid #f59e0b' : '1px solid #e5e7eb' }};border-radius:6px;font-size:12px;background:{{ $rate->needs_rate_update ? '#fffbeb' : 'white' }};">
                                <button class="btn btn-sm {{ $rate->needs_rate_update ? 'btn-warning' : 'btn-primary' }}">
                                    {{ $rate->needs_rate_update ? 'Set' : 'Save' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            @if($rate->needs_rate_update)
                                <span class="badge badge-warning">N/A</span>
                            @else
                                <span class="badge {{ $rate->is_active ? 'badge-success' : 'badge-danger' }}">{{ $rate->is_active ? 'On' : 'Off' }}</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.rates.destroy', $rate) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm" style="color:#ef4444;">Del</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:24px;color:#9ca3af;">No rates configured</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $rates->links() }}
    </div>
</div>
@endsection
