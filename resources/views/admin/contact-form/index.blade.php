@extends('layouts.admin')
@section('title', 'Contact Form Submitter')
@section('page-title', 'Contact Form Submitter')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fff1f2;border:1px solid #fca5a5;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:14px;color:#991b1b;font-weight:600;">
    {{ session('error') }}
</div>
@endif

{{-- Daily usage bar --}}
<div class="card" style="margin-bottom:20px;padding:16px 20px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
        <div style="font-size:13px;font-weight:600;color:#374151;">Daily Usage</div>
        <div style="font-size:13px;font-weight:700;color:{{ $todayCount >= $dailyLimit ? '#ef4444' : '#01BF63' }};">
            {{ $todayCount }} / {{ $dailyLimit }} submissions today
        </div>
    </div>
    @php $pct = min(100, round($todayCount / $dailyLimit * 100)); @endphp
    <div style="background:#f3f4f6;border-radius:999px;height:8px;overflow:hidden;">
        <div style="width:{{ $pct }}%;height:100%;background:{{ $pct >= 100 ? '#ef4444' : ($pct >= 75 ? '#f59e0b' : '#01BF63') }};border-radius:999px;transition:width .3s;"></div>
    </div>
    <div style="font-size:12px;color:#9ca3af;margin-top:6px;">Resets at midnight. Limit is {{ $dailyLimit }} per day.</div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

    {{-- Form --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:4px;">Submit a Contact Form</div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:24px;">
            Paste the URL of a page that has a contact form. The system will detect the form fields and submit your message automatically.
        </p>

        <form method="POST" action="{{ route('admin.contact-form.submit') }}">
            @csrf

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Contact Page URL</label>
                <input type="url" name="url" class="form-control"
                       value="{{ old('url') }}"
                       placeholder="https://example.com/contact"
                       required>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Must be the page that contains the contact form — not the homepage.</div>
                @error('url')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label">Your Name</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', 'Installs Bank') }}" required maxlength="100">
                    @error('name')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Your Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', 'contact@installsbank.com') }}" required maxlength="200">
                    @error('email')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Subject <span style="font-weight:400;color:#9ca3af;">(if the form has a subject field)</span></label>
                <input type="text" name="subject" class="form-control"
                       value="{{ old('subject', 'Partnership Opportunity — Installs Bank') }}" maxlength="200">
                @error('subject')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="10" required maxlength="5000"
                          style="font-size:13px;line-height:1.7;resize:vertical;">{{ old('message', $defaultMessage) }}</textarea>
                @error('message')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            @if($todayCount >= $dailyLimit)
            <div style="background:#fff1f2;border:1px solid #fca5a5;border-radius:10px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;">
                Daily limit reached. You can submit again tomorrow.
            </div>
            @else
            <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;font-size:15px;"
                    onclick="return confirm('Submit message to this contact form?')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;margin-top:-2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Submit Contact Form
            </button>
            @endif
        </form>
    </div>

    {{-- Tips sidebar --}}
    <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:24px;">

        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">How it works</div>
            <div style="display:flex;flex-direction:column;gap:10px;font-size:13px;color:#6b7280;line-height:1.6;">
                <div style="display:flex;gap:10px;">
                    <div style="width:20px;height:20px;background:#01BF63;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">1</div>
                    <div>Fetches the contact page HTML</div>
                </div>
                <div style="display:flex;gap:10px;">
                    <div style="width:20px;height:20px;background:#01BF63;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">2</div>
                    <div>Detects name, email, subject & message fields</div>
                </div>
                <div style="display:flex;gap:10px;">
                    <div style="width:20px;height:20px;background:#01BF63;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">3</div>
                    <div>Fills in your details and submits</div>
                </div>
                <div style="display:flex;gap:10px;">
                    <div style="width:20px;height:20px;background:#01BF63;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">4</div>
                    <div>Reports success or failure</div>
                </div>
            </div>
        </div>

        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:10px;">Limitations</div>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;color:#6b7280;line-height:1.6;">
                <div>✗ CAPTCHA-protected forms will fail</div>
                <div>✗ JavaScript-rendered forms won't work</div>
                <div>✗ Login-required forms can't be submitted</div>
                <div>✓ WordPress Contact Form 7 works well</div>
                <div>✓ Most static HTML forms work</div>
            </div>
        </div>

    </div>
</div>

{{-- Recent Submissions Log --}}
@if($recent->count())
<div class="card" style="margin-top:24px;">
    <div class="card-title" style="margin-bottom:16px;">Recent Submissions</div>
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="border-bottom:2px solid #f3f4f6;">
                <th style="text-align:left;padding:8px 12px;color:#6b7280;font-weight:600;">URL</th>
                <th style="text-align:left;padding:8px 12px;color:#6b7280;font-weight:600;">Sent As</th>
                <th style="text-align:left;padding:8px 12px;color:#6b7280;font-weight:600;">Status</th>
                <th style="text-align:left;padding:8px 12px;color:#6b7280;font-weight:600;">Note</th>
                <th style="text-align:left;padding:8px 12px;color:#6b7280;font-weight:600;">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recent as $row)
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:10px 12px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <a href="{{ $row->url }}" target="_blank" style="color:#01BF63;text-decoration:none;" title="{{ $row->url }}">
                        {{ parse_url($row->url, PHP_URL_HOST) }}
                    </a>
                </td>
                <td style="padding:10px 12px;color:#374151;">{{ $row->sender_name }}</td>
                <td style="padding:10px 12px;">
                    @if($row->status === 'sent')
                        <span style="background:#f0fdf4;color:#166534;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600;">Sent</span>
                    @else
                        <span style="background:#fff1f2;color:#991b1b;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600;">Failed</span>
                    @endif
                </td>
                <td style="padding:10px 12px;color:#6b7280;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $row->note }}">{{ $row->note }}</td>
                <td style="padding:10px 12px;color:#9ca3af;white-space:nowrap;">{{ $row->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection
