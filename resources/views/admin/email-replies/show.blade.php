@extends('layouts.admin')
@section('title', $emailReply->subject)
@section('page-title', 'Email Inbox')

@section('content')

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.email-replies.index') }}"
       style="font-size:13px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-weight:500;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Inbox
    </a>
</div>

@php
    // Split body into new reply vs quoted original
    $rawBody      = $emailReply->body ?? '';
    $lines        = explode("\n", $rawBody);
    $newLines     = [];
    $quotedLines  = [];
    $inQuote      = false;

    foreach ($lines as $line) {
        $trimmed = ltrim($line);
        // "On ... wrote:" separator pattern
        if (!$inQuote && preg_match('/^On .+wrote:$/s', trim($rawBody)) === false
            && preg_match('/^On\s.+\swrote:/', $trimmed)) {
            $inQuote = true;
        }
        if ($inQuote || str_starts_with($trimmed, '>')) {
            $inQuote = true;
            $quotedLines[] = $line;
        } else {
            $newLines[] = $line;
        }
    }

    $newContent    = trim(implode("\n", $newLines));
    $quotedContent = trim(implode("\n", $quotedLines));
    // Strip leading > from quoted lines for display
    $quotedDisplay = implode("\n", array_map(fn($l) => preg_replace('/^>\s?/', '', $l), $quotedLines));
@endphp

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

    {{-- Main email card --}}
    <div class="card" style="padding:0;overflow:hidden;">

        {{-- Email header --}}
        <div style="padding:24px 28px;border-bottom:1px solid #f3f4f6;">
            <h2 style="font-size:18px;font-weight:700;color:#111827;margin:0 0 18px;">
                {{ $emailReply->subject }}
            </h2>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    {{-- Avatar --}}
                    <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr($emailReply->from_name ?: $emailReply->from_email, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:600;color:#111827;">
                            {{ $emailReply->from_name ?: $emailReply->from_email }}
                        </div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:1px;">
                            @if($emailReply->from_name)
                                {{ $emailReply->from_email }}
                            @endif
                            &nbsp;→&nbsp; contact@installsbank.com
                        </div>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:13px;color:#374151;font-weight:500;">
                        {{ $emailReply->received_at->format('M j, Y \a\t g:i A') }}
                    </div>
                    <div style="font-size:12px;color:#9ca3af;margin-top:2px;">
                        {{ $emailReply->received_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Actual reply content --}}
        <div style="padding:28px;border-bottom:{{ $quotedContent ? '1px solid #f3f4f6' : 'none' }};">
            @if($newContent)
                <div style="font-size:15px;color:#111827;line-height:1.8;white-space:pre-wrap;font-family:inherit;">{{ $newContent }}</div>
            @else
                <div style="font-size:14px;color:#9ca3af;font-style:italic;">(empty reply)</div>
            @endif
        </div>

        {{-- Quoted / original email (collapsed) --}}
        @if($quotedContent)
        <div style="padding:0 28px 20px;">
            <button onclick="toggleQuote()" id="quoteToggle"
                    style="background:none;border:none;cursor:pointer;font-size:12px;color:#9ca3af;display:flex;align-items:center;gap:6px;padding:12px 0;font-weight:500;">
                <svg id="quoteChevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                     style="transition:transform .2s;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
                Show original message
            </button>
            <div id="quotedBlock" style="display:none;border-left:3px solid #e5e7eb;padding-left:16px;margin-top:4px;">
                <div style="font-size:13px;color:#9ca3af;line-height:1.7;white-space:pre-wrap;font-family:inherit;">{{ $quotedDisplay }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Reply action --}}
        <div class="card" style="padding:18px;">
            <div style="font-size:12px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;">Actions</div>
            <a href="mailto:{{ $emailReply->from_email }}?subject=Re: {{ rawurlencode($emailReply->subject) }}"
               style="display:flex;align-items:center;justify-content:center;gap:8px;background:#111827;color:#fff;text-decoration:none;border-radius:8px;padding:11px 16px;font-size:13px;font-weight:600;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Reply in Email Client
            </a>
            <div style="font-size:11px;color:#9ca3af;text-align:center;margin-top:8px;">Opens your default email app</div>
        </div>

        {{-- Broadcast match --}}
        <div class="card" style="padding:18px;">
            <div style="font-size:12px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;">Broadcast Match</div>
            @if($emailReply->matchedLog)
            @php $log = $emailReply->matchedLog; @endphp
            <div style="display:flex;align-items:flex-start;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px;">
                <div style="width:20px;height:20px;background:#16a34a;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                    <svg width="11" height="11" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div style="font-size:13px;line-height:1.6;">
                    <div style="font-weight:600;color:#166534;margin-bottom:4px;">Matched to a sent broadcast</div>
                    <div style="color:#374151;">To: <strong>{{ $log->recipient_email }}</strong></div>
                    <div style="color:#6b7280;font-size:12px;margin-top:2px;">{{ Str::limit($log->subject, 38) }}</div>
                    <div style="color:#9ca3af;font-size:12px;margin-top:2px;">Sent {{ $log->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @else
            <div style="font-size:13px;color:#9ca3af;background:#f9fafb;border-radius:8px;padding:12px;text-align:center;">
                No matching broadcast found for<br>
                <strong style="color:#374151;">{{ $emailReply->from_email }}</strong>
            </div>
            @endif
        </div>

        {{-- Sender info --}}
        <div class="card" style="padding:18px;">
            <div style="font-size:12px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;">Sender</div>
            <div style="font-size:13px;color:#374151;line-height:1.8;">
                @if($emailReply->from_name)
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:#9ca3af;">Name</span>
                    <span style="font-weight:600;">{{ $emailReply->from_name }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;gap:8px;">
                    <span style="color:#9ca3af;flex-shrink:0;">Email</span>
                    <span style="font-weight:600;word-break:break-all;text-align:right;">{{ $emailReply->from_email }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:#9ca3af;">Received</span>
                    <span>{{ $emailReply->received_at->format('M j, Y') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleQuote() {
    const block   = document.getElementById('quotedBlock');
    const chevron = document.getElementById('quoteChevron');
    const btn     = document.getElementById('quoteToggle');
    const open    = block.style.display === 'none';
    block.style.display   = open ? 'block' : 'none';
    chevron.style.transform = open ? 'rotate(180deg)' : '';
    btn.querySelector('span') && (btn.querySelectorAll('*')[1].textContent = open ? 'Hide original message' : 'Show original message');
    btn.childNodes[2].textContent = open ? ' Hide original message' : ' Show original message';
}
</script>
@endsection
