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

<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">

    {{-- Compose Form --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:4px;">Compose Email</div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:24px;">Email is sent from <strong>contact@installsbank.com</strong> via your configured SMTP.</p>

        <form method="POST" action="{{ route('admin.broadcast-email.send') }}" id="broadcastForm">
            @csrf

            {{-- Recipients --}}
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Send To</label>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:2px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:border-color .15s;" id="lbl-all">
                        <input type="radio" name="recipients" value="all" {{ old('recipients','all')==='all'?'checked':'' }} onchange="handleRecipient(this)">
                        <div>
                            <div style="font-size:14px;font-weight:600;color:#111827;">All Publishers</div>
                            <div style="font-size:12px;color:#6b7280;">Send to all {{ $publishers->count() }} registered publishers</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:2px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:border-color .15s;" id="lbl-active">
                        <input type="radio" name="recipients" value="active" {{ old('recipients')==='active'?'checked':'' }} onchange="handleRecipient(this)">
                        <div>
                            <div style="font-size:14px;font-weight:600;color:#111827;">Active Publishers Only</div>
                            <div style="font-size:12px;color:#6b7280;">{{ $publishers->where('status','active')->count() }} active publishers</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border:2px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:border-color .15s;" id="lbl-specific">
                        <input type="radio" name="recipients" value="specific" {{ old('recipients')==='specific'?'checked':'' }} onchange="handleRecipient(this)">
                        <div>
                            <div style="font-size:14px;font-weight:600;color:#111827;">Specific Publishers</div>
                            <div style="font-size:12px;color:#6b7280;">Choose individual recipients</div>
                        </div>
                    </label>
                </div>
                @error('recipients')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Specific publisher picker --}}
            <div id="specificPicker" style="display:none;margin-bottom:20px;">
                <label class="form-label">Select Publishers</label>
                <div style="border:1px solid #e5e7eb;border-radius:10px;max-height:220px;overflow-y:auto;padding:8px;">
                    @foreach($publishers as $pub)
                    <label style="display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:6px;cursor:pointer;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                        <input type="checkbox" name="specific_ids[]" value="{{ $pub->id }}" {{ is_array(old('specific_ids')) && in_array($pub->id, old('specific_ids')) ? 'checked' : '' }}>
                        <span style="font-size:13px;color:#374151;">{{ $pub->name }}</span>
                        <span style="font-size:12px;color:#9ca3af;margin-left:2px;">{{ $pub->email }}</span>
                        @if($pub->status === 'active')
                        <span style="margin-left:auto;background:#d1fae5;color:#065f46;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;">Active</span>
                        @else
                        <span style="margin-left:auto;background:#f3f4f6;color:#6b7280;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;">{{ ucfirst($pub->status) }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @error('specific_ids')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
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
                    <span style="font-weight:400;color:#9ca3af;margin-left:6px;">(the promotional image is always included at the top)</span>
                </label>
                <textarea name="body" id="emailBody" class="form-control" rows="18" required maxlength="10000"
                          style="font-family:monospace;font-size:13px;line-height:1.7;">{{ old('body', $defaultBody) }}</textarea>
                @error('body')<div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;text-align:right;">
                    <span id="charCount">0</span> / 10,000 characters
                </div>
            </div>

            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:#78350f;">
                <strong>Note:</strong> Emails are sent one-by-one. Sending to many publishers at once may take a moment. Do not close this page until you see the success message.
            </div>

            <button type="submit" class="btn btn-primary" onclick="return confirmSend()"
                style="width:100%;padding:13px;font-size:15px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:6px;margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Send Email
            </button>
        </form>
    </div>

    {{-- Right: Preview --}}
    <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:24px;">

        {{-- Image preview --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:10px;">Image Included in Email</div>
            <img src="https://installsbank.com/images/installs-bank-mail.webp"
                 alt="Installs Bank"
                 style="width:100%;border-radius:8px;border:1px solid #e5e7eb;">
            <div style="font-size:11px;color:#9ca3af;margin-top:8px;text-align:center;">This image appears at the top of every sent email.</div>
        </div>

        {{-- Tips --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">Tips</div>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;color:#6b7280;line-height:1.6;">
                <div>✓ Keep the subject line short and compelling</div>
                <div>✓ Personalization is automatic — the publisher's name is not added automatically, write "Hello," for a general greeting</div>
                <div>✓ Test with a specific publisher first before sending to all</div>
                <div>✓ Avoid spam trigger words like "FREE", "WINNER", "URGENT"</div>
            </div>
        </div>

    </div>
</div>

<script>
// Highlight selected recipient option
function handleRecipient(radio) {
    document.querySelectorAll('[id^="lbl-"]').forEach(l => l.style.borderColor = '#e5e7eb');
    const map = {all:'lbl-all', active:'lbl-active', specific:'lbl-specific'};
    document.getElementById(map[radio.value]).style.borderColor = '#01BF63';
    document.getElementById('specificPicker').style.display = radio.value === 'specific' ? 'block' : 'none';
}

// Init on load
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="recipients"]:checked');
    if (checked) handleRecipient(checked);

    // Char counter
    const ta = document.getElementById('emailBody');
    const counter = document.getElementById('charCount');
    function updateCount() { counter.textContent = ta.value.length.toLocaleString(); }
    ta.addEventListener('input', updateCount);
    updateCount();
});

function confirmSend() {
    const rec = document.querySelector('input[name="recipients"]:checked')?.value;
    const labels = {all: 'ALL publishers', active: 'all ACTIVE publishers', specific: 'selected publishers'};
    return confirm('Send this email to ' + (labels[rec] || 'publishers') + '?\n\nThis cannot be undone.');
}
</script>
@endsection
