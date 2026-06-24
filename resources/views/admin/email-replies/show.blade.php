@extends('layouts.admin')
@section('title', $emailReply->subject)
@section('page-title', 'Email Reply')

@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('admin.email-replies.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Inbox
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

    {{-- Email body --}}
    <div class="card">
        <div style="border-bottom:1px solid #f3f4f6;padding-bottom:16px;margin-bottom:20px;">
            <div style="font-size:20px;font-weight:700;color:#111827;margin-bottom:14px;line-height:1.4;">
                {{ $emailReply->subject }}
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#374151;flex-shrink:0;">
                    {{ strtoupper(substr($emailReply->from_name ?: $emailReply->from_email, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:14px;font-weight:600;color:#111827;">
                        {{ $emailReply->from_name ?: $emailReply->from_email }}
                    </div>
                    @if($emailReply->from_name)
                    <div style="font-size:12px;color:#9ca3af;">{{ $emailReply->from_email }}</div>
                    @endif
                </div>
                <div style="margin-left:auto;font-size:13px;color:#9ca3af;text-align:right;">
                    {{ $emailReply->received_at->format('M j, Y \a\t g:i A') }}<br>
                    <span style="font-size:12px;">{{ $emailReply->received_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div style="font-size:14px;color:#374151;line-height:1.8;white-space:pre-wrap;font-family:inherit;">{{ $emailReply->body ?: '(empty message)' }}</div>
    </div>

    {{-- Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Actions --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">Actions</div>
            <a href="mailto:{{ $emailReply->from_email }}?subject=Re: {{ rawurlencode($emailReply->subject) }}"
               class="btn btn-primary" style="width:100%;text-align:center;display:block;margin-bottom:8px;">
                Reply via Email Client
            </a>
            <div style="font-size:11px;color:#9ca3af;text-align:center;">Opens your default email app</div>
        </div>

        {{-- Match info --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">Broadcast Match</div>
            @if($emailReply->matchedLog)
            @php $log = $emailReply->matchedLog; @endphp
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 12px;font-size:13px;">
                <div style="color:#166534;font-weight:600;margin-bottom:6px;">✓ Matched to a sent broadcast</div>
                <div style="color:#374151;margin-bottom:4px;">
                    <span style="color:#6b7280;">To:</span> {{ $log->recipient_email }}
                </div>
                <div style="color:#374151;margin-bottom:4px;">
                    <span style="color:#6b7280;">Subject:</span> {{ Str::limit($log->subject, 35) }}
                </div>
                <div style="color:#374151;">
                    <span style="color:#6b7280;">Sent:</span> {{ $log->created_at->diffForHumans() }}
                </div>
            </div>
            @else
            <div style="font-size:13px;color:#9ca3af;">
                No matching broadcast record found for <strong>{{ $emailReply->from_email }}</strong>.
            </div>
            @endif
        </div>

        {{-- Sender info --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">Sender</div>
            <div style="font-size:13px;color:#374151;word-break:break-all;">
                <div style="margin-bottom:6px;"><span style="color:#6b7280;">Email:</span> {{ $emailReply->from_email }}</div>
                @if($emailReply->from_name)
                <div><span style="color:#6b7280;">Name:</span> {{ $emailReply->from_name }}</div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
