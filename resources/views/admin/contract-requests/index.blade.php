@extends('layouts.admin')
@section('title', 'Contract Change Requests')
@section('page-title', 'Contract Change Requests')

@section('content')

@if(session('success'))
<div class="alert" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">{{ session('error') }}</div>
@endif

{{-- Stats row --}}
@php
    $pending  = $requests->where('status', 'pending')->count();
    $approved = $requests->where('status', 'approved')->count();
    $rejected = $requests->where('status', 'rejected')->count();
@endphp
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px;">
    <div style="background:white;border:1px solid #fde68a;border-radius:12px;padding:18px 20px;">
        <div style="font-size:11px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Pending</div>
        <div style="font-size:28px;font-weight:800;color:#78350f;">{{ $requests->where('status','pending')->total() ?? $pending }}</div>
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
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">From</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Requested</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Reason</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Date</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
                <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $r)
            @php
                $typeLabel = fn($t) => match($t) { 'per_click' => 'Per-Click', 'fixed' => 'Fixed Daily', 'installs_base' => 'Installs Base', default => ucfirst($t) };
                $sc = match($r->status) {
                    'approved' => ['bg'=>'#d1fae5','text'=>'#065f46'],
                    'rejected' => ['bg'=>'#fee2e2','text'=>'#991b1b'],
                    default    => ['bg'=>'#fef3c7','text'=>'#92400e'],
                };
            @endphp
            <tr style="border-bottom:1px solid #f3f4f6;" id="row-{{ $r->id }}">
                <td style="padding:12px 14px;">
                    <div style="font-weight:700;color:#111827;">{{ $r->user->name }}</div>
                    <div style="font-size:11px;color:#6b7280;">{{ $r->user->email }}</div>
                </td>
                <td style="padding:12px 14px;color:#374151;">{{ $typeLabel($r->current_type) }}</td>
                <td style="padding:12px 14px;font-weight:700;color:#111827;">{{ $typeLabel($r->requested_type) }}</td>
                <td style="padding:12px 14px;color:#6b7280;max-width:200px;">
                    <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $r->reason ?? '—' }}</span>
                </td>
                <td style="padding:12px 14px;color:#6b7280;white-space:nowrap;">{{ $r->created_at->format('M d, Y') }}</td>
                <td style="padding:12px 14px;">
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">{{ ucfirst($r->status) }}</span>
                    @if($r->admin_note)
                    <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $r->admin_note }}</div>
                    @endif
                </td>
                <td style="padding:12px 14px;">
                    @if($r->status === 'pending')
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        {{-- Approve --}}
                        <form method="POST" action="{{ route('admin.contract-requests.approve', $r) }}" style="display:flex;gap:6px;align-items:center;">
                            @csrf
                            <input type="text" name="admin_note" placeholder="Note (optional)" style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;width:130px;">
                            <button type="submit" style="background:#01BF63;color:white;border:none;border-radius:6px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">✓ Approve</button>
                        </form>
                        {{-- Reject --}}
                        <form method="POST" action="{{ route('admin.contract-requests.reject', $r) }}" style="display:flex;gap:6px;align-items:center;" onsubmit="return confirm('Reject this contract change request?')">
                            @csrf
                            <input type="text" name="admin_note" placeholder="Reason (optional)" style="padding:5px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;width:130px;">
                            <button type="submit" style="background:#ef4444;color:white;border:none;border-radius:6px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">✗ Reject</button>
                        </form>
                    </div>
                    @else
                    <span style="color:#9ca3af;font-size:12px;">{{ $r->responded_at?->format('M d') ?? '—' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding:40px;text-align:center;color:#9ca3af;font-size:14px;">No contract change requests yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($requests->hasPages())
<div style="margin-top:20px;">{{ $requests->links() }}</div>
@endif
@endsection
