@extends('layouts.admin')
@section('title', 'Traffic Reports')
@section('page-title', 'Traffic Testing Reports')

@section('content')
<div style="max-width:720px;">

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

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">From Date *</label>
                    <input type="date" name="date_from" class="form-control" value="{{ old('date_from', now()->subDays(6)->toDateString()) }}" required>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">To Date *</label>
                    <input type="date" name="date_to" class="form-control" value="{{ old('date_to', now()->toDateString()) }}" required>
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
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Leave as "All Traffic" for a platform-wide report, or pick a specific account.</div>
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

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Report Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', 'Traffic Testing Report') }}" placeholder="Traffic Testing Report">
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Prepared For (optional)</label>
                    <input type="text" name="prepared_for" class="form-control" value="{{ old('prepared_for') }}" placeholder="Client / Advertiser name">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Fetch Data →</button>
        </form>
    </div>
</div>
@endsection
