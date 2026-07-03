@extends('layouts.admin')
@section('title', 'Publisher Website Requests')
@section('page-title', 'Publisher Website Requests')

@section('content')
@if(session('success'))
<div class="alert" style="background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;margin-bottom:20px;">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;margin-bottom:20px;">✗ {{ session('error') }}</div>
@endif

<!-- Filter tabs -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('admin.publisher-websites.index') }}"
       class="btn {{ !request('status') || request('status') === 'pending' ? 'btn-primary' : 'btn-ghost' }} btn-sm">
        Pending
        @if($pendingCount > 0)<span style="background:#fff;color:#065f46;font-size:11px;font-weight:700;padding:1px 7px;border-radius:10px;margin-left:4px;">{{ $pendingCount }}</span>@endif
    </a>
    <a href="{{ route('admin.publisher-websites.index', ['status' => 'approved']) }}"
       class="btn {{ request('status') === 'approved' ? 'btn-primary' : 'btn-ghost' }} btn-sm">Approved</a>
    <a href="{{ route('admin.publisher-websites.index', ['status' => 'rejected']) }}"
       class="btn {{ request('status') === 'rejected' ? 'btn-primary' : 'btn-ghost' }} btn-sm">Rejected</a>
    <a href="{{ route('admin.publisher-websites.index', ['status' => 'all']) }}"
       class="btn {{ request('status') === 'all' ? 'btn-primary' : 'btn-ghost' }} btn-sm">All</a>
</div>

<div class="card">
    <div class="card-title mb-4">
        {{ ucfirst(request('status', 'pending')) === 'All' ? 'All' : ucfirst(request('status', 'pending')) }} Website Requests
    </div>

    @forelse($websites as $website)
    <div style="padding:20px 0;border-bottom:1px solid #f3f4f6;" id="website-{{ $website->id }}">
        <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">
            <!-- Info -->
            <div style="flex:1;min-width:240px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                    <span style="font-size:15px;font-weight:700;">{{ $website->domain }}</span>
                    <span class="badge {{ $website->status === 'approved' ? 'badge-success' : ($website->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                        {{ ucfirst($website->status) }}
                    </span>
                </div>
                <div style="font-size:12px;color:#6b7280;margin-bottom:4px;">
                    Full URL: <a href="{{ $website->website_url }}" target="_blank" style="color:#01BF63;">{{ $website->website_url }}</a>
                </div>
                <div style="font-size:12px;color:#6b7280;margin-bottom:4px;">
                    @if($website->user->role === 'reseller')
                        Reseller: <a href="{{ route('admin.resellers.show', $website->user) }}" style="color:#7c3aed;font-weight:600;">{{ $website->user->name }}</a>
                        <span style="background:#ede9fe;color:#6d28d9;padding:1px 8px;border-radius:10px;font-size:10px;font-weight:800;">RESELLER</span>
                    @else
                        Publisher: <a href="{{ route('admin.publishers.show', $website->user) }}" style="color:#3b82f6;font-weight:600;">{{ $website->user->name }}</a>
                    @endif
                    <span style="color:#9ca3af;">({{ $website->user->email }})</span>
                </div>
                <div style="font-size:11px;color:#9ca3af;">Submitted {{ $website->created_at->diffForHumans() }}</div>

                @if($website->isRejected() && $website->rejection_reason)
                <div style="margin-top:8px;padding:8px 12px;background:#fee2e2;border-radius:6px;font-size:12px;color:#991b1b;">
                    <strong>Rejection reason:</strong> {{ $website->rejection_reason }}
                </div>
                @endif

                @if($website->isApproved() && $website->trackingLink)
                @php $wlink = $website->trackingLink; @endphp
                <div style="margin-top:8px;padding:8px 12px;background:{{ $wlink->is_active ? '#d1fae5' : '#fef3c7' }};border-radius:6px;font-size:12px;color:{{ $wlink->is_active ? '#065f46' : '#92400e' }};">
                    <strong>Ad code {{ $wlink->is_active ? '' : '(SUSPENDED) ' }}:</strong> <span style="font-family:monospace;">{{ $wlink->tracking_url }}</span>
                    <span style="color:#6b7280;margin-left:6px;">· domain: <strong>{{ $wlink->trackingDomain?->domain ?? 'installsbank.com (default)' }}</strong></span>
                </div>
                @endif
            </div>

            <!-- Screenshots -->
            @if($website->stat_screenshots && count($website->stat_screenshots) > 0)
            <div style="display:flex;gap:8px;flex-wrap:wrap;flex-shrink:0;">
                @foreach($website->stat_screenshots as $i => $path)
                <a href="{{ asset('storage/' . $path) }}" target="_blank"
                   style="display:block;border-radius:6px;overflow:hidden;border:1.5px solid #e5e7eb;transition:border-color 0.15s;"
                   onmouseover="this.style.borderColor='#01BF63'" onmouseout="this.style.borderColor='#e5e7eb'">
                    <img src="{{ asset('storage/' . $path) }}" alt="Screenshot {{ $i+1 }}"
                         style="width:100px;height:70px;object-fit:cover;display:block;">
                </a>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Approve / Reject actions (only for pending) -->
        @if($website->isPending())
        <div style="margin-top:16px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <!-- Approve form -->
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;">
                <div style="font-size:13px;font-weight:700;color:#065f46;margin-bottom:12px;">✓ Approve & Create Ad Code</div>
                <form method="POST" action="{{ route('admin.publisher-websites.approve', $website) }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:10px;">
                        <label class="form-label" style="font-size:12px;">Tracking Domain (ad code URL)</label>
                        <select name="tracking_domain_id" class="form-control form-select" style="font-size:12px;">
                            <option value="">Default — installsbank.com</option>
                            @foreach($domains as $d)
                                <option value="{{ $d->id }}">{{ $d->domain }}{{ $d->label ? ' — ' . $d->label : '' }}</option>
                            @endforeach
                        </select>
                        <div style="font-size:10px;color:#059669;margin-top:3px;">Pick a custom domain to serve this reseller's/publisher's ad code URL.</div>
                    </div>
                    <div class="form-group" style="margin-bottom:10px;">
                        <label class="form-label" style="font-size:12px;">Destination URL (Windows / Main) *</label>
                        <input type="url" name="original_url" class="form-control" style="font-size:12px;" required placeholder="https://example.com/landing">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                        <div>
                            <label class="form-label" style="font-size:11px;">URL Windows (optional)</label>
                            <input type="url" name="url_windows" class="form-control" style="font-size:12px;" placeholder="https://...">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:11px;">URL Android (optional)</label>
                            <input type="url" name="url_android" class="form-control" style="font-size:12px;" placeholder="https://...">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:11px;">URL Mac/iOS (optional)</label>
                            <input type="url" name="url_mac" class="form-control" style="font-size:12px;" placeholder="https://...">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:11px;">URL Other (optional)</label>
                            <input type="url" name="url_other" class="form-control" style="font-size:12px;" placeholder="https://...">
                        </div>
                    </div>
                    <div style="background:#e6faf2;border-radius:6px;padding:8px 10px;font-size:11px;color:#065f46;margin-bottom:12px;">
                        Ad code will only count clicks from referrer: <strong>{{ $website->domain }}</strong>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm" style="width:100%;">Approve & Create Ad Code</button>
                </form>
            </div>

            <!-- Reject form -->
            <div style="background:#fff5f5;border:1px solid #fecaca;border-radius:8px;padding:16px;">
                <div style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:12px;">✗ Reject Request</div>
                <form method="POST" action="{{ route('admin.publisher-websites.reject', $website) }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label" style="font-size:12px;">Reason for rejection *</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" style="font-size:12px;" required
                            placeholder="e.g. Website is behind Cloudflare, low-quality traffic, no valid statistics..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;"
                            onclick="return confirm('Reject this website request?')">Reject</button>
                </form>
            </div>
        </div>
        @endif

        <!-- Manage actions (approved websites): change domain, suspend, delete -->
        @if($website->isApproved() && $website->trackingLink)
        @php $wlink = $website->trackingLink; @endphp
        <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <!-- Change domain -->
            <form method="POST" action="{{ route('admin.publisher-websites.change-domain', $website) }}"
                  style="display:flex;gap:8px;align-items:flex-end;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;">
                @csrf
                <div>
                    <label class="form-label" style="font-size:11px;">Ad Code Domain</label>
                    <select name="tracking_domain_id" class="form-control form-select" style="font-size:12px;min-width:200px;">
                        <option value="">Default — installsbank.com</option>
                        @foreach($domains as $d)
                            <option value="{{ $d->id }}" {{ $wlink->tracking_domain_id == $d->id ? 'selected' : '' }}>
                                {{ $d->domain }}{{ $d->label ? ' — ' . $d->label : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update Domain</button>
            </form>

            <!-- Suspend / resume -->
            <form method="POST" action="{{ route('admin.publisher-websites.toggle-suspend', $website) }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-sm"
                        style="background:{{ $wlink->is_active ? '#fffbeb' : '#f0fdf4' }};color:{{ $wlink->is_active ? '#92400e' : '#065f46' }};border:1px solid {{ $wlink->is_active ? '#fde68a' : '#bbf7d0' }};"
                        onclick="return confirm('{{ $wlink->is_active ? 'Suspend this ad code? It will stop counting clicks immediately.' : 'Resume this ad code?' }}')">
                    {{ $wlink->is_active ? '⏸ Suspend Ad Code' : '▶ Resume Ad Code' }}
                </button>
            </form>

            <!-- Delete -->
            <form method="POST" action="{{ route('admin.publisher-websites.destroy', $website) }}" style="margin:0;"
                  onsubmit="return confirm('DELETE {{ addslashes($website->domain) }}?\n\nThis permanently removes the website and its ad code, including that link\'s click log. The user\'s earnings totals are unaffected.\n\nUse Suspend instead if you only want to pause it. This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">🗑 Delete Website</button>
            </form>
        </div>
        @endif
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:#9ca3af;">
        <div style="font-size:40px;margin-bottom:8px;">🌐</div>
        <div>No {{ request('status', 'pending') }} website requests.</div>
    </div>
    @endforelse

    @if($websites->hasPages())
    <div style="margin-top:20px;">{{ $websites->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
