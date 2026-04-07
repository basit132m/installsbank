@extends('layouts.admin')
@section('title', 'Install Settings')
@section('page-title', 'Install Settings')

@section('content')
@php
    $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
@endphp

<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;" class="settings-layout">

    {{-- Main Form --}}
    <div class="card">
        <div style="font-size:15px;font-weight:700;margin-bottom:4px;">Clicks per Install — Per Weekday Settings</div>
        <div style="font-size:13px;color:var(--gray-500);margin-bottom:24px;">
            Configure how many Windows clicks count as one install, per day of the week.
        </div>

        <form method="POST" action="{{ route('admin.install-settings.update') }}">
            @csrf
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Clicks per Install (Ratio)</th>
                            <th style="font-size:11px;color:#9ca3af;">Example: 50 clicks = 1 install</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ratios as $ratio)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:32px;height:32px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#6b7280;">
                                        {{ substr($dayNames[$ratio->weekday], 0, 3) }}
                                    </div>
                                    <span style="font-weight:600;font-size:14px;">{{ $dayNames[$ratio->weekday] }}</span>
                                </div>
                            </td>
                            <td>
                                <input type="number"
                                       name="ratios[{{ $ratio->weekday }}]"
                                       value="{{ $ratio->ratio }}"
                                       min="1" max="1000" step="1"
                                       style="width:120px;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;outline:none;transition:border-color 0.15s;"
                                       onfocus="this.style.borderColor='#01BF63'"
                                       onblur="this.style.borderColor='#d1d5db'">
                            </td>
                            <td style="color:#9ca3af;font-size:13px;">
                                1 install per <strong>{{ $ratio->ratio }}</strong> Windows clicks
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">Save Ratios</button>
            </div>
        </form>
    </div>

    {{-- Info Sidebar --}}
    <div>
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:16px;font-size:13px;color:#92400e;line-height:1.7;margin-bottom:16px;">
            <strong>Privacy by Design</strong><br>
            The ratio varies per day to prevent publishers from identifying the exact conversion pattern. The publisher sees install counts and earnings, not the underlying clicks-per-install ratio.
        </div>

        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:16px;font-size:13px;color:#1e40af;line-height:1.7;margin-bottom:16px;">
            <strong>How it works:</strong><br>
            1. A Windows click comes in from a publisher on an <em>Installs Based</em> contract.<br>
            2. The system increments a pending counter for that country.<br>
            3. When the counter reaches today's ratio, <strong>1 install</strong> is credited.<br>
            4. The remainder carries over to the next batch.<br>
            5. Earnings = installs × country install rate.
        </div>

        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:16px;font-size:13px;color:#166534;line-height:1.7;">
            <strong>Recommended range:</strong><br>
            20–100 clicks per install is typical. Lower values = more installs credited. Set rates in <a href="{{ route('admin.install-rates.index') }}" style="color:#16a34a;font-weight:600;">Install Rates</a>.
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
@media(max-width:900px){ .settings-layout { grid-template-columns:1fr !important; } }
</style>
@endpush
