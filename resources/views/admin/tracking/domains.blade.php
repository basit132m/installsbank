@extends('layouts.admin')
@section('title', 'Tracking Domains')
@section('page-title', 'Tracking Domains')

@section('content')
<div class="flex-between mb-6">
    <div>
        <div style="font-size:14px;color:var(--gray-500);margin-top:4px;">Add custom domains to hide your main installsbank.com domain from tracking URLs.</div>
    </div>
    <a href="{{ route('admin.tracking.index') }}" class="btn btn-ghost btn-sm">← Tracking Links</a>
</div>

<div class="grid-2" style="gap:24px;align-items:start;">

    <!-- Add domain form -->
    <div class="card">
        <div class="card-title mb-1">Add New Tracking Domain</div>
        <p class="text-muted text-sm mb-4">Point your custom domain's DNS (A record or CNAME) to this server first, then add it here.</p>
        <form method="POST" action="{{ route('admin.tracking-domains.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Domain</label>
                <input type="text" name="domain" class="form-control" value="{{ old('domain') }}"
                    placeholder="track.yourdomain.com" required>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Enter domain only — no http:// or trailing slash.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Label (Optional)</label>
                <input type="text" name="label" class="form-control" value="{{ old('label') }}"
                    placeholder="e.g. Campaign Domain A">
            </div>
            <button type="submit" class="btn btn-primary">Add Domain</button>
        </form>

        <div style="margin-top:20px;padding:14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
            <div style="font-size:13px;font-weight:700;color:#065f46;margin-bottom:6px;">Setup Instructions</div>
            <ol style="font-size:13px;color:#065f46;padding-left:18px;line-height:1.8;">
                <li>Buy a domain (e.g. from Namecheap, GoDaddy)</li>
                <li>Go to DNS settings of that domain</li>
                <li>Add an A record pointing to your server IP</li>
                <li>In Hostinger → Domains → add it as an addon domain pointing to the same <code>public_html</code> folder</li>
                <li>Add it here — done!</li>
            </ol>
        </div>
    </div>

    <!-- Domain list -->
    <div class="card">
        <div class="card-title mb-4">Active Domains</div>
        @if($domains->isEmpty())
            <div style="text-align:center;padding:32px;color:var(--gray-400);">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                No tracking domains yet.<br>
                <span style="font-size:12px;">Add one on the left to get started.</span>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Links</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($domains as $domain)
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13px;">{{ $domain->domain }}</div>
                                @if($domain->label)
                                    <div style="font-size:12px;color:var(--gray-400);">{{ $domain->label }}</div>
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ $domain->tracking_links_count }} links</span></td>
                            <td>
                                @if($domain->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-gray">Disabled</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <form method="POST" action="{{ route('admin.tracking-domains.toggle', $domain) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ghost">
                                            {{ $domain->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.tracking-domains.destroy', $domain) }}"
                                        onsubmit="return confirm('Delete this domain? All links using it will revert to the default domain.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
