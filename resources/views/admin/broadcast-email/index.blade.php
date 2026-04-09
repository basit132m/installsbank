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

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

    {{-- Compose Form --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:4px;">Compose Email</div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:24px;">Sent from <strong>contact@installsbank.com</strong>. Add one or multiple email addresses below.</p>

        <form method="POST" action="{{ route('admin.broadcast-email.send') }}">
            @csrf

            {{-- Email addresses input --}}
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Recipient Email Addresses</label>
                <div style="position:relative;">
                    <textarea name="emails" id="emailsInput" class="form-control" rows="4"
                              placeholder="Enter email addresses separated by comma, semicolon, or new line:&#10;john@example.com, jane@example.com&#10;another@example.com"
                              style="font-family:monospace;font-size:13px;resize:vertical;">{{ old('emails') }}</textarea>
                </div>
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
                       value="{{ old('subject', 'Earn More with Installs Bank — High Payouts on Every Click & Install') }}"
                       required maxlength="200">
                @error('subject')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Body --}}
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">
                    Email Body
                    <span style="font-weight:400;color:#9ca3af;margin-left:6px;">(promotional image is always included at the top)</span>
                </label>
                <textarea name="body" id="emailBody" class="form-control" rows="18" required maxlength="10000"
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

<script>
// Count valid emails as user types
const emailsInput = document.getElementById('emailsInput');
const emailCount  = document.getElementById('emailCount');

function countEmails() {
    const parts = emailsInput.value.split(/[\s,;]+/).filter(e => e.trim() !== '');
    emailCount.textContent = parts.length;
}

emailsInput.addEventListener('input', countEmails);
countEmails();

// Char counter for body
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
</script>
@endsection
