@extends('layouts.admin')
@section('title', 'Country Rates')
@section('page-title', 'Country Click Rates')

@section('content')
<div class="grid-2 mb-6" style="align-items:start;">
    <!-- Add Rate -->
    <div class="card">
        <div class="card-title mb-4">Add Country Rate</div>
        <form method="POST" action="{{ route('admin.rates.store') }}">
            @csrf
            <div class="form-group"><label class="form-label">Country Code</label><input type="text" name="country_code" class="form-control" placeholder="US" maxlength="5" required style="text-transform:uppercase;"></div>
            <div class="form-group"><label class="form-label">Country Name</label><input type="text" name="country_name" class="form-control" placeholder="United States" required></div>
            <div class="form-group"><label class="form-label">Rate Per Click (USD)</label><input type="number" name="rate_per_click" class="form-control" placeholder="0.000500" step="0.000001" min="0" required></div>
            <button type="submit" class="btn btn-primary">Add Rate</button>
        </form>
    </div>

    <!-- Rates Table -->
    <div class="card">
        <div class="flex-between mb-4">
            <div class="card-title">All Country Rates</div>
            <span style="font-size:13px;color:#9ca3af;">{{ $rates->total() }} countries</span>
        </div>
        <div class="table-wrap" style="max-height:400px;overflow-y:auto;">
            <table>
                <thead><tr><th>Country</th><th>Code</th><th>Rate/Click</th><th>Active</th><th></th></tr></thead>
                <tbody>
                    @forelse($rates as $rate)
                    <tr>
                        <td style="font-size:13px;">{{ $rate->country_name }}</td>
                        <td><code>{{ $rate->country_code }}</code></td>
                        <td>
                            <form method="POST" action="{{ route('admin.rates.update', $rate) }}" style="display:flex;gap:6px;align-items:center;">
                                @csrf @method('PUT')
                                <input type="number" name="rate_per_click" value="{{ $rate->rate_per_click }}" step="0.000001" style="width:90px;padding:4px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;">
                                <button class="btn btn-primary btn-sm">Save</button>
                            </form>
                        </td>
                        <td><span class="badge {{ $rate->is_active ? 'badge-success' : 'badge-danger' }}">{{ $rate->is_active ? 'On' : 'Off' }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.rates.destroy', $rate) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-ghost btn-sm" style="color:#ef4444;">Del</button></form>
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
