@extends('layouts.admin')
@section('title', 'Rate Increase Requests')
@section('page-title', 'Rate Increase Requests')

@section('content')

@if(session('success'))
<div class="alert" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">{{ session('error') }}</div>
@endif

{{-- Stats row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px;">
    <div style="background:white;border:1px solid #fde68a;border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Pending</div>
        <div style="font-size:28px;font-weight:800;color:#78350f;">{{ $pendingCount }}</div>
    </div>
    <div style="background:white;border:1px solid #bbf7d0;border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Approved</div>
        <div style="font-size:28px;font-weight:800;color:#166534;">{{ $approved }}</div>
    </div>
    <div style="background:white;border:1px solid #fca5a5;border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Rejected</div>
        <div style="font-size:28px;font-weight:800;color:#991b1b;">{{ $rejected }}</div>
    </div>
</div>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="border-bottom:2px solid #f3f4f6;">
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Publisher</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Current Rate</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Requested</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Justification</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Screenshots</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Date</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $r)
            @php
                $sc = match($r->status) {
                    'approved' => ['bg'=>'#d1fae5','text'=>'#065f46'],
                    'rejected' => ['bg'=>'#fee2e2','text'=>'#991b1b'],
                    default    => ['bg'=>'#fef3c7','text'=>'#92400e'],
                };
            @endphp
            <tr style="border-bottom:1px solid #f3f4f6;">
                <td style="padding:12px 14px;">
                    <div style="font-weight:700;color:#111827;">{{ $r->user->name }}</div>
                    <div style="font-size:11px;color:#6b7280;">{{ $r->user->email }}</div>
                </td>
                <td style="padding:12px 14px;color:#374151;font-weight:600;">${{ number_format($r->current_rate, 4) }}<span style="font-size:11px;font-weight:400;color:#9ca3af;">/day</span></td>
                <td style="padding:12px 14px;font-weight:700;color:#01BF63;">${{ number_format($r->requested_rate, 4) }}<span style="font-size:11px;font-weight:400;color:#9ca3af;">/day</span></td>
                <td style="padding:12px 14px;color:#6b7280;max-width:200px;">
                    <span style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;font-size:12px;">{{ $r->justification ?? '—' }}</span>
                </td>
                <td style="padding:12px 14px;">
                    @if($r->stats_screenshots && count($r->stats_screenshots) > 0)
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        @foreach($r->stats_screenshots as $i => $path)
                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                           style="font-size:12px;color:#3b82f6;text-decoration:none;display:flex;align-items:center;gap:4px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Screenshot {{ $i + 1 }}
                        </a>
                        @endforeach
                    </div>
                    @else
                    <span style="color:#9ca3af;font-size:12px;">None</span>
                    @endif
                </td>
                <td style="padding:12px 14px;color:#6b7280;white-space:nowrap;">{{ $r->created_at->format('M d, Y') }}</td>
                <td style="padding:12px 14px;">
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">{{ ucfirst($r->status) }}</span>
                    @if($r->status === 'approved' && $r->effective_from)
                    <div style="font-size:11px;color:#6b7280;margin-top:3px;">Effective {{ $r->effective_from->format('M 1, Y') }}</div>
                    @endif
                    @if($r->admin_note)
                    <div style="font-size:11px;color:#6b7280;margin-top:3px;">{{ $r->admin_note }}</div>
                    @endif
                </td>
                <td style="padding:12px 14px;">
                    @if($r->status === 'pending')
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        {{-- Approve --}}
                        <form method="POST" action="{{ route('admin.rate-increase-requests.approve', $r) }}" style="display:flex;flex-direction:column;gap:4px;">
                            @csrf
                            <div style="display:flex;gap:4px;align-items:center;">
                                <span style="font-size:11px;color:#6b7280;white-space:nowrap;">Rate $</span>
                                <input type="number" name="approved_rate" step="0.0001" min="0.0001"
                                       value="{{ $r->requested_rate }}"
                                       style="padding:5px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;width:80px;">
                                <span style="font-size:11px;color:#6b7280;">/day</span>
                            </div>
                            <div style="display:flex;gap:4px;align-items:center;">
                                <input type="text" name="admin_note" placeholder="Note (optional)" style="padding:5px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;width:110px;">
                                <button type="submit" style="background:#01BF63;color:white;border:none;border-radius:6px;padding:6px 10px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">✓ Approve</button>
                            </div>
                        </form>
                        {{-- Reject --}}
                        <form method="POST" action="{{ route('admin.rate-increase-requests.reject', $r) }}" style="display:flex;gap:4px;align-items:center;" onsubmit="return confirm('Reject this rate increase request?')">
                            @csrf
                            <input type="text" name="admin_note" placeholder="Reason (optional)" style="padding:5px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;width:130px;">
                            <button type="submit" style="background:#ef4444;color:white;border:none;border-radius:6px;padding:6px 10px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">✗ Reject</button>
                        </form>
                    </div>
                    @else
                    <span style="color:#9ca3af;font-size:12px;">{{ $r->responded_at?->format('M d') ?? '—' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:40px;text-align:center;color:#9ca3af;font-size:14px;">No rate increase requests yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($requests->hasPages())
<div style="margin-top:20px;">{{ $requests->links() }}</div>
@endif
@endsection
