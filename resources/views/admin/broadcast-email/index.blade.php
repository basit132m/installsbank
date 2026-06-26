@extends('layouts.admin')
@section('title', 'Send Broadcast Email')
@section('page-title', 'Send Broadcast Email')

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

{{-- Duplicate warning --}}
@if(session('duplicate_warning'))
@php $dups = session('duplicate_warning'); @endphp
<div style="background:#fffbeb;border:1px solid #f59e0b;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
        <svg width="20" height="20" fill="none" stroke="#b45309" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        <div style="font-size:14px;font-weight:700;color:#92400e;">
            {{ count($dups) }} email address{{ count($dups) > 1 ? 'es were' : ' was' }} already contacted in the last 30 days
        </div>
    </div>
    <div style="background:#fff;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:14px;max-height:180px;overflow-y:auto;">
        @foreach($dups as $dup)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid #fef3c7;font-size:13px;">
            <span style="font-weight:600;color:#374151;">{{ $dup['recipient_email'] }}</span>
            <span style="color:#9ca3af;font-size:12px;">
                Last sent: {{ \Carbon\Carbon::parse($dup['created_at'])->diffForHumans() }}
                &nbsp;·&nbsp; Subject: {{ Str::limit($dup['subject'], 40) }}
            </span>
        </div>
        @endforeach
    </div>
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <div style="font-size:13px;color:#78350f;">Do you want to send to these addresses again anyway?</div>
        <button type="button" onclick="forceResendSubmit()"
                style="background:#d97706;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:700;cursor:pointer;">
            Yes, Send Anyway
        </button>
        <span style="font-size:12px;color:#9ca3af;">or edit your recipient list above and resubmit.</span>
    </div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

    {{-- Compose Form --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:4px;">Compose Email</div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">Select a sender address then add recipient emails below.</p>

        <form method="POST" action="{{ route('admin.broadcast-email.send') }}" id="broadcastForm">
            @csrf
            <input type="hidden" name="force_resend" id="forceResend" value="0">

            {{-- From Email selector --}}
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">From Email Address</label>
                <select name="from_email" class="form-control" style="font-size:13px;">
                    @php
                        $fromEmails = [
                            'contact@installsbank.com',
                            'info@installsbank.com',
                            'admin@installsbank.com',
                            'team@installsbank.com',
                            'manager@installsbank.com',
                        ];
                        $selectedFrom = old('from_email', 'contact@installsbank.com');
                    @endphp
                    @foreach($fromEmails as $fe)
                        <option value="{{ $fe }}" {{ $selectedFrom === $fe ? 'selected' : '' }}>{{ $fe }}</option>
                    @endforeach
                </select>
                @error('from_email')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Email addresses input --}}
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Recipient Email Addresses</label>
                <textarea name="emails" id="emailsInput" class="form-control" rows="4"
                          placeholder="Enter email addresses separated by comma, semicolon, or new line:&#10;john@example.com, jane@example.com&#10;another@example.com"
                          style="font-family:monospace;font-size:13px;resize:vertical;">{{ old('emails') }}</textarea>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                    <div style="font-size:12px;color:#9ca3af;">Separate with comma, semicolon, or new line. Duplicates are removed automatically.</div>
                    <div style="font-size:12px;font-weight:600;color:#374151;white-space:nowrap;margin-left:12px;">
                        <span id="emailCount">0</span> email(s)
                    </div>
                </div>
                @error('emails')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Subject --}}
            <div class="form-group" style="margin-bottom:16px;">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control"
                       value="{{ old('subject', 'Get Advance Payment — Earn More with Installs Bank') }}"
                       required maxlength="200">
                @error('subject')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Body --}}
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">
                    Opening Message
                    <span style="font-weight:400;color:#9ca3af;margin-left:6px;">(intro paragraph only — links, features, CTA &amp; contact section are added automatically)</span>
                </label>
                <textarea name="body" id="emailBody" class="form-control" rows="6" required maxlength="10000"
                          style="font-family:monospace;font-size:13px;line-height:1.7;">{{ old('body', $defaultBody) }}</textarea>
                @error('body')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;text-align:right;">
                    <span id="charCount">0</span> / 10,000 characters
                </div>
            </div>

            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:#78350f;">
                <strong>Note:</strong> Emails are sent one-by-one. Do not close this page until you see the success message.
            </div>

            <button type="submit" class="btn btn-primary" onclick="return confirmSend()"
                    style="width:100%;padding:13px;font-size:15px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="display:inline;vertical-align:middle;margin-right:6px;margin-top:-2px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Send Email
            </button>
        </form>
    </div>

    {{-- Right panel --}}
    <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:24px;">

        {{-- Image preview --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:10px;">Image Included in Email</div>
            <img src="https://installsbank.com/images/installs-bank-mail.webp"
                 alt="Installs Bank"
                 style="width:100%;border-radius:8px;border:1px solid #e5e7eb;">
            <div style="font-size:11px;color:#9ca3af;margin-top:8px;text-align:center;">Appears at the top of every sent email.</div>
        </div>

        {{-- Tips --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">Tips</div>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;color:#6b7280;line-height:1.6;">
                <div>✓ You can paste a list of emails from a spreadsheet</div>
                <div>✓ Comma, semicolon, or line-break separated all work</div>
                <div>✓ Invalid addresses are skipped and reported</div>
                <div>✓ Duplicate addresses are removed automatically</div>
                <div>✓ Test with one email first before bulk sending</div>
            </div>
        </div>

    </div>
</div>

{{-- Send History --}}
<div class="card" style="padding:0;overflow:hidden;margin-top:28px;">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="font-size:15px;font-weight:700;color:#111827;">Send History</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Most recent 50 sends shown. Each row is one recipient.</div>
        </div>
        <div style="font-size:13px;color:#6b7280;">
            Total: <strong>{{ $history->total() }}</strong> records
        </div>
    </div>

    @if($history->isEmpty())
    <div style="text-align:center;padding:48px;color:#9ca3af;font-size:14px;">
        No emails sent yet.
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>From</th>
                    <th>Recipient</th>
                    <th>Subject</th>
                    <th style="text-align:center;">Status</th>
                    <th>Sent By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $log)
                <tr>
                    <td style="white-space:nowrap;font-size:12px;color:#6b7280;">
                        {{ $log->created_at->format('M j, Y') }}<br>
                        <span style="color:#9ca3af;">{{ $log->created_at->format('g:i A') }}</span>
                    </td>
                    <td style="font-size:12px;color:#6b7280;white-space:nowrap;">
                        {{ $log->from_email ?? 'contact@installsbank.com' }}
                    </td>
                    <td style="font-size:13px;font-weight:600;color:#111827;">
                        {{ $log->recipient_email }}
                    </td>
                    <td style="font-size:13px;color:#374151;max-width:260px;">
                        {{ Str::limit($log->subject, 50) }}
                    </td>
                    <td style="text-align:center;">
                        @if($log->status === 'sent')
                            <span style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">Sent</span>
                        @else
                            <span style="background:#fff1f2;color:#991b1b;border:1px solid #fca5a5;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;"
                                  title="{{ $log->error_message }}">Failed</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#6b7280;">
                        {{ $log->sender?->name ?? 'Unknown' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($history->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f3f4f6;">
        {{ $history->links() }}
    </div>
    @endif
    @endif
</div>

<script>
const emailsInput = document.getElementById('emailsInput');
const emailCount  = document.getElementById('emailCount');

function countEmails() {
    const parts = emailsInput.value.split(/[\s,;]+/).filter(e => e.trim() !== '');
    emailCount.textContent = parts.length;
}

emailsInput.addEventListener('input', countEmails);
countEmails();

const ta      = document.getElementById('emailBody');
const counter = document.getElementById('charCount');
function updateCount() { counter.textContent = ta.value.length.toLocaleString(); }
ta.addEventListener('input', updateCount);
updateCount();

function confirmSend() {
    const count = parseInt(emailCount.textContent) || 0;
    if (count === 0) { alert('Please enter at least one email address.'); return false; }
    return confirm('Send this email to ' + count + ' address(es)?\n\nThis cannot be undone.');
}

function forceResendSubmit() {
    if (!confirm('This will send to all addresses including those contacted recently. Continue?')) return;
    document.getElementById('forceResend').value = '1';
    document.getElementById('broadcastForm').submit();
}
</script>
@endsection
