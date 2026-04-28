@extends('layouts.admin')
@section('title', 'Tracking Links')
@section('page-title', 'Tracking Links')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:13px 18px;margin-bottom:20px;font-size:14px;color:#166534;font-weight:600;">
    ✓ {{ session('success') }}
</div>
@endif

<!-- Header -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="font-size:14px;color:#6b7280;">Manage all tracking links — view assignments, reassign publishers, toggle or delete.</div>
    </div>
    <a href="{{ route('admin.tracking.create') }}" class="btn btn-primary">+ Create Link</a>
</div>

<!-- Ad Code Status Legend -->
<div style="display:flex;gap:16px;align-items:center;margin-bottom:16px;font-size:12px;color:#6b7280;flex-wrap:wrap;">
    <span style="font-weight:700;color:#374151;">Ad Code Activity:</span>
    <span style="display:flex;align-items:center;gap:5px;"><span style="width:9px;height:9px;border-radius:50%;background:#10b981;display:inline-block;"></span>Active (within 2h)</span>
    <span style="display:flex;align-items:center;gap:5px;"><span style="width:9px;height:9px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>Idle (2–6h)</span>
    <span style="display:flex;align-items:center;gap:5px;"><span style="width:9px;height:9px;border-radius:50%;background:#ef4444;display:inline-block;"></span>Inactive (6h+)</span>
    <span style="display:flex;align-items:center;gap:5px;"><span style="width:9px;height:9px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>No clicks yet</span>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb;">
                    <th style="text-align:left;padding:11px 16px;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.04em;">Publisher</th>
                    <th style="text-align:left;padding:11px 16px;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.04em;">Link</th>
                    <th style="text-align:left;padding:11px 16px;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.04em;">Destination</th>
                    <th style="text-align:left;padding:11px 16px;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.04em;">Clicks</th>
                    <th style="text-align:left;padding:11px 16px;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.04em;">Activity</th>
                    <th style="text-align:left;padding:11px 16px;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.04em;">Status</th>
                    <th style="text-align:right;padding:11px 16px;font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.04em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($links as $link)
                @php
                    $lastClick    = $link->last_click_at;
                    $hoursSince   = $lastClick ? now()->diffInHours($lastClick) : null;
                    if ($lastClick === null) {
                        $adDot = '#d1d5db'; $adLabel = 'No clicks yet'; $adBg = '#f9fafb'; $adText = '#9ca3af';
                    } elseif ($hoursSince <= 2) {
                        $adDot = '#10b981'; $adLabel = 'Active'; $adBg = '#d1fae5'; $adText = '#065f46';
                    } elseif ($hoursSince <= 6) {
                        $adDot = '#f59e0b'; $adLabel = 'Idle'; $adBg = '#fef3c7'; $adText = '#92400e';
                    } else {
                        $adDot = '#ef4444'; $adLabel = 'Inactive'; $adBg = '#fee2e2'; $adText = '#991b1b';
                    }
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">

                    {{-- Publisher --}}
                    <td style="padding:13px 16px;vertical-align:middle;">
                        <div style="display:flex;align-items:center;gap:9px;">
                            @php
                                $initials = collect(explode(' ', $link->user->name ?? '?'))->map(fn($w)=>strtoupper($w[0]??''))->take(2)->implode('');
                            @endphp
                            <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#01BF63,#059669);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:white;flex-shrink:0;">
                                {{ $initials }}
                            </div>
                            <div>
                                <a href="{{ route('admin.publishers.show', $link->user) }}" style="font-size:13px;font-weight:700;color:#111827;text-decoration:none;" onmouseover="this.style.color='#01BF63'" onmouseout="this.style.color='#111827'">
                                    {{ $link->user->name }}
                                </a>
                                <div style="font-size:11px;color:#9ca3af;">{{ $link->user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Link name + code --}}
                    <td style="padding:13px 16px;vertical-align:middle;">
                        <div style="font-size:13px;font-weight:600;color:#374151;">{{ $link->name ?: 'Unnamed Link' }}</div>
                        <div style="display:flex;align-items:center;gap:6px;margin-top:3px;">
                            <code style="font-size:11px;background:#f3f4f6;padding:2px 7px;border-radius:4px;color:#374151;">{{ $link->unique_code }}</code>
                            @if($link->allowed_domain)
                            <span style="font-size:10px;background:#eff6ff;color:#1d4ed8;padding:1px 6px;border-radius:4px;font-weight:600;">🔒 {{ $link->allowed_domain }}</span>
                            @endif
                        </div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:3px;font-family:monospace;">{{ $link->tracking_url }}</div>
                    </td>

                    {{-- Destination --}}
                    <td style="padding:13px 16px;vertical-align:middle;max-width:180px;">
                        <a href="{{ $link->original_url }}" target="_blank" style="font-size:12px;color:#6b7280;text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $link->original_url }}" onmouseover="this.style.color='#01BF63'" onmouseout="this.style.color='#6b7280'">
                            {{ $link->original_url }}
                        </a>
                        @if($link->url_windows || $link->url_android || $link->url_mac)
                        <div style="display:flex;gap:4px;margin-top:4px;flex-wrap:wrap;">
                            @if($link->url_windows)<span style="font-size:10px;background:#dbeafe;color:#1e40af;padding:1px 5px;border-radius:3px;font-weight:600;">WIN</span>@endif
                            @if($link->url_android)<span style="font-size:10px;background:#d1fae5;color:#065f46;padding:1px 5px;border-radius:3px;font-weight:600;">AND</span>@endif
                            @if($link->url_mac)<span style="font-size:10px;background:#f3f4f6;color:#374151;padding:1px 5px;border-radius:3px;font-weight:600;">MAC</span>@endif
                        </div>
                        @endif
                    </td>

                    {{-- Clicks --}}
                    <td style="padding:13px 16px;vertical-align:middle;">
                        <div style="font-size:15px;font-weight:800;color:#111827;">{{ number_format($link->unique_clicks) }}</div>
                        <div style="font-size:11px;color:#ef4444;font-weight:600;">{{ number_format($link->fraud_clicks) }} fraud</div>
                    </td>

                    {{-- Activity --}}
                    <td style="padding:13px 16px;vertical-align:middle;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:{{ $adDot }};display:inline-block;flex-shrink:0;"></span>
                            <span style="background:{{ $adBg }};color:{{ $adText }};padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">{{ $adLabel }}</span>
                        </div>
                        @if($lastClick)
                        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">{{ $lastClick->format('M d, H:i') }}</div>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td style="padding:13px 16px;vertical-align:middle;">
                        <span class="badge {{ $link->is_active ? 'badge-success' : 'badge-danger' }}">{{ $link->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>

                    {{-- Actions --}}
                    <td style="padding:13px 16px;vertical-align:middle;text-align:right;">
                        <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
                            <button onclick="openReassign({{ $link->id }}, '{{ addslashes($link->unique_code) }}', '{{ addslashes($link->name ?: 'Unnamed Link') }}', {{ $link->user_id }})"
                                style="padding:5px 10px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">
                                ↔ Reassign
                            </button>
                            <a href="{{ route('admin.tracking.edit', $link) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.tracking.toggle', $link) }}">@csrf
                                <button class="btn btn-ghost btn-sm">{{ $link->is_active ? 'Pause' : 'Activate' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.tracking.destroy', $link) }}" onsubmit="return confirm('Delete link {{ $link->unique_code }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <svg width="32" height="32" fill="none" stroke="#d1d5db" viewBox="0 0 24 24" stroke-width="1.5" style="margin:0 auto 8px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        No tracking links yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($links->hasPages())
    <div style="padding:14px 16px;border-top:1px solid #f3f4f6;">
        {{ $links->links() }}
    </div>
    @endif
</div>

<!-- Reassign Publisher Modal -->
<div id="reassignModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:16px;padding:28px;width:100%;max-width:460px;margin:16px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <div>
                <div style="font-size:16px;font-weight:800;color:#111827;">Reassign Publisher</div>
                <div style="font-size:12px;color:#9ca3af;margin-top:2px;">Move this link to a different publisher account</div>
            </div>
            <button onclick="closeReassign()" style="width:32px;height:32px;border-radius:8px;border:1px solid #e5e7eb;background:#f9fafb;cursor:pointer;font-size:18px;color:#6b7280;display:flex;align-items:center;justify-content:center;">×</button>
        </div>

        <!-- Link info box -->
        <div style="background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:20px;">
            <div style="font-size:12px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:6px;">Link being reassigned</div>
            <div style="font-size:14px;font-weight:700;color:#111827;" id="modalLinkName"></div>
            <code style="font-size:12px;color:#6b7280;" id="modalLinkCode"></code>
        </div>

        <form id="reassignForm" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Assign to Publisher</label>
                <select name="user_id" id="reassignSelect" class="form-control form-select" style="font-size:14px;">
                    @foreach($publishers as $pub)
                    <option value="{{ $pub->id }}" data-email="{{ $pub->email }}">{{ $pub->name }} — {{ $pub->email }}</option>
                    @endforeach
                </select>
                <div style="font-size:12px;color:#9ca3af;margin-top:6px;" id="currentlyAssigned"></div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" style="flex:1;padding:11px;background:#01BF63;color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;">
                    Confirm Reassign
                </button>
                <button type="button" onclick="closeReassign()" style="padding:11px 18px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openReassign(linkId, code, name, currentUserId) {
    document.getElementById('modalLinkName').textContent = name;
    document.getElementById('modalLinkCode').textContent  = code;
    document.getElementById('reassignForm').action = '/admin/tracking/' + linkId + '/reassign';

    const select = document.getElementById('reassignSelect');
    // Pre-select current publisher
    for (let opt of select.options) {
        opt.selected = (parseInt(opt.value) === currentUserId);
    }
    updateCurrentLabel();

    const modal = document.getElementById('reassignModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeReassign() {
    document.getElementById('reassignModal').style.display = 'none';
    document.body.style.overflow = '';
}

function updateCurrentLabel() {
    const select = document.getElementById('reassignSelect');
    const opt    = select.options[select.selectedIndex];
    document.getElementById('currentlyAssigned').textContent =
        opt ? 'Selected: ' + opt.text : '';
}

document.getElementById('reassignSelect').addEventListener('change', updateCurrentLabel);

// Close on backdrop click
document.getElementById('reassignModal').addEventListener('click', function(e) {
    if (e.target === this) closeReassign();
});

// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeReassign();
});
</script>
@endpush
