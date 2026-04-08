@extends('layouts.admin')
@section('title', 'Blacklisted Domains')
@section('page-title', 'Blacklisted Domains')

@section('content')

@if(session('success'))
<div class="alert" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">{{ session('error') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">

    {{-- Domain list --}}
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div>
                <div class="card-title" style="margin-bottom:2px;">Blacklisted Domains</div>
                <div style="font-size:12px;color:#6b7280;">Clicks from these domains are never tracked or credited.</div>
            </div>
            <span style="background:#fee2e2;color:#991b1b;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;">{{ $domains->total() }} domains</span>
        </div>

        @if($domains->isEmpty())
        <div style="text-align:center;padding:48px 24px;color:#9ca3af;">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            <div style="font-size:14px;font-weight:600;">No domains blacklisted yet</div>
            <div style="font-size:13px;margin-top:4px;">Add a domain using the form to block clicks from it.</div>
        </div>
        @else
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f3f4f6;">
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Domain</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Reason</th>
                    <th style="text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Added</th>
                    <th style="padding:8px 12px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($domains as $d)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 12px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0;"></div>
                            <span style="font-weight:600;color:#111827;font-family:monospace;font-size:13px;">{{ $d->domain }}</span>
                        </div>
                    </td>
                    <td style="padding:10px 12px;color:#6b7280;max-width:260px;">{{ $d->reason ?? '—' }}</td>
                    <td style="padding:10px 12px;color:#6b7280;white-space:nowrap;">{{ $d->created_at->format('M d, Y') }}</td>
                    <td style="padding:10px 12px;text-align:right;">
                        <form method="POST" action="{{ route('admin.blacklisted-domains.destroy', $d) }}"
                              onsubmit="return confirm('Remove {{ $d->domain }} from blacklist?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;border-radius:6px;padding:5px 12px;font-size:12px;font-weight:600;cursor:pointer;">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($domains->hasPages())
        <div style="margin-top:16px;">{{ $domains->links() }}</div>
        @endif
        @endif
    </div>

    {{-- Add domain form --}}
    <div style="position:sticky;top:90px;">
        <div class="card" style="border:1.5px solid #fca5a5;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
                <div style="width:38px;height:38px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <div>
                    <div class="card-title" style="margin-bottom:1px;">Blacklist a Domain</div>
                    <div style="font-size:11px;color:#6b7280;">No clicks from this domain will be tracked.</div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.blacklisted-domains.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Domain</label>
                    <input type="text" name="domain" class="form-control" placeholder="example.com" required
                           style="font-family:monospace;">
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">www. prefix and https:// are stripped automatically.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Reason <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
                    <input type="text" name="reason" class="form-control" placeholder="e.g. Bot traffic, Fraud source..." maxlength="500">
                </div>
                <button type="submit" class="btn btn-danger" style="width:100%;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Add to Blacklist
                </button>
            </form>
        </div>

        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-top:16px;">
            <div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:6px;">How it works</div>
            <ul style="font-size:12px;color:#78350f;line-height:1.7;padding-left:16px;margin:0;">
                <li>Clicks whose HTTP referrer matches a blacklisted domain are silently marked as fraud.</li>
                <li>The click is still recorded in the database but is not counted or credited.</li>
                <li>Subdomain matching is exact — blacklisting <code>spam.com</code> does not block <code>sub.spam.com</code>. Add each variant separately.</li>
                <li>The blacklist is cached for 1 hour for performance.</li>
            </ul>
        </div>
    </div>

</div>
@endsection
