@extends('layouts.admin')
@section('title', 'Tracking Domains')
@section('page-title', 'Tracking Domains')

@section('content')
<div class="flex-between mb-6">
    <div>
        <div style="font-size:14px;color:var(--gray-500);margin-top:4px;">Add custom domains to protect your main installsbank.com domain from appearing in tracking URLs.</div>
    </div>
    <a href="{{ route('admin.tracking.index') }}" class="btn btn-ghost btn-sm">← Tracking Links</a>
</div>

{{-- Server Info Bar --}}
<div style="background:#1f2937;border-radius:12px;padding:16px 24px;margin-bottom:24px;display:flex;align-items:center;gap:32px;flex-wrap:wrap;">
    <div>
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;margin-bottom:4px;">Server IPv4</div>
        <code style="color:#34d399;font-size:15px;font-weight:700;">{{ $serverIpv4 }}</code>
    </div>
    @if($serverIpv6)
    <div>
        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;margin-bottom:4px;">Server IPv6</div>
        <code style="color:#34d399;font-size:15px;font-weight:700;">{{ $serverIpv6 }}</code>
    </div>
    @endif
    <div style="margin-left:auto;font-size:13px;color:#6b7280;">Use these IPs when setting DNS records for any new tracking domain.</div>
</div>

<div class="grid-2" style="gap:24px;align-items:start;">

    {{-- Add Domain Form --}}
    <div class="card">
        <div class="card-title mb-1">Add New Tracking Domain</div>
        <p class="text-muted text-sm mb-4">Complete all setup steps below before adding the domain here.</p>

        <form method="POST" action="{{ route('admin.tracking-domains.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Domain</label>
                <input type="text" name="domain" class="form-control" value="{{ old('domain') }}"
                    placeholder="downloadpirate.org" required>
                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">No http:// — just the domain e.g. <code>downloadpirate.org</code></div>
            </div>
            <div class="form-group">
                <label class="form-label">Label (Optional)</label>
                <input type="text" name="label" class="form-control" value="{{ old('label') }}"
                    placeholder="e.g. Campaign Domain A">
            </div>
            <button type="submit" class="btn btn-primary">Add Domain</button>
        </form>

        {{-- Step by step guide --}}
        <div style="margin-top:24px;">
            <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:12px;">Complete Setup Guide</div>

            <div style="display:flex;flex-direction:column;gap:10px;">

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#111827;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">1</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#111827;">Buy a new domain</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">From Namecheap, GoDaddy, or any registrar. It should be a completely different domain — not a subdomain of installsbank.com.</div>
                    </div>
                </div>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#111827;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">2</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#111827;">Point nameservers to Hostinger</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">In your registrar (e.g. Namecheap), set the nameservers to Hostinger's:</div>
                        <code style="font-size:12px;color:#059652;display:block;margin-top:4px;">ns1.dns-parking.com<br>ns2.dns-parking.com</code>
                    </div>
                </div>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#111827;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">3</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#111827;">Add domain to THIS Hostinger account</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">In Hostinger hPanel → Websites → Add Website → enter the new domain. Make sure it's added to the <strong>same account</strong> as installsbank.com.</div>
                    </div>
                </div>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#111827;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">4</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#111827;">Add DNS A record</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">In Hostinger hPanel → DNS Zone Editor for the new domain, add:</div>
                        <div style="background:#111827;border-radius:6px;padding:8px 10px;margin-top:6px;">
                            <code style="font-size:12px;color:#34d399;">Type: A &nbsp;&nbsp; Name: @ &nbsp;&nbsp; Value: {{ $serverIpv4 }}</code>
                        </div>
                        @if($serverIpv6)
                        <div style="background:#111827;border-radius:6px;padding:8px 10px;margin-top:4px;">
                            <code style="font-size:12px;color:#34d399;">Type: AAAA &nbsp; Name: @ &nbsp;&nbsp; Value: {{ $serverIpv6 }}</code>
                        </div>
                        @endif
                    </div>
                </div>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#111827;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">5</div>
                    <div style="width:100%;">
                        <div style="font-size:13px;font-weight:600;color:#111827;">Run this command in SSH (PuTTY) — replace YOUR-DOMAIN</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">Run once. Links that domain to the Laravel app:</div>
                        <div style="background:#111827;border-radius:6px;padding:10px 12px;margin-top:6px;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                            <code id="generic-cmd" style="font-size:12px;color:#34d399;word-break:break-all;">rm -rf ~/domains/<span style="color:#fbbf24;">YOUR-DOMAIN</span>/public_html && ln -s ~/domains/installsbank.com/public_html ~/domains/<span style="color:#fbbf24;">YOUR-DOMAIN</span>/public_html</code>
                        </div>
                    </div>
                </div>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#111827;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">6</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#111827;">Set PHP version to 8.4</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">In Hostinger hPanel → PHP Configuration — select the new domain and set PHP version to <strong>8.4</strong>.</div>
                    </div>
                </div>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#111827;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">7</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#111827;">Enable SSL</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:2px;">In Hostinger hPanel → SSL — install the free SSL certificate for the new domain.</div>
                    </div>
                </div>

                <div style="background:#e6faf2;border:1px solid #a7f3d0;border-radius:8px;padding:12px 14px;display:flex;gap:12px;">
                    <div style="width:24px;height:24px;background:#01BF63;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">8</div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#065f46;">Add it above and assign to a tracking link</div>
                        <div style="font-size:12px;color:#065f46;margin-top:2px;">Enter the domain in the form above, then go to Tracking Links → Edit any link → select this domain from the dropdown.</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Domain List --}}
    <div class="card">
        <div class="card-title mb-4">Added Domains</div>
        @if($domains->isEmpty())
            <div style="text-align:center;padding:32px;color:var(--gray-400);">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                No tracking domains yet.<br>
                <span style="font-size:12px;">Follow the guide on the left to add your first one.</span>
            </div>
        @else
            @foreach($domains as $domain)
            <div style="border:1px solid #e5e7eb;border-radius:10px;padding:14px 16px;margin-bottom:12px;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                    <div>
                        <div style="font-weight:700;font-size:14px;color:#111827;">{{ $domain->domain }}</div>
                        @if($domain->label)
                            <div style="font-size:12px;color:#9ca3af;">{{ $domain->label }}</div>
                        @endif
                        <div style="margin-top:4px;display:flex;gap:8px;align-items:center;">
                            @if($domain->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-gray">Disabled</span>
                            @endif
                            <span class="badge badge-info">{{ $domain->tracking_links_count }} links</span>
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <form method="POST" action="{{ route('admin.tracking-domains.toggle', $domain) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ghost">{{ $domain->is_active ? 'Disable' : 'Enable' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.tracking-domains.destroy', $domain) }}"
                            onsubmit="return confirm('Delete {{ $domain->domain }}? All links using it will revert to the default domain.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>

                {{-- SSH Command for this domain --}}
                <div style="margin-top:12px;background:#111827;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:10px;">
                    <code style="font-size:12px;color:#34d399;flex:1;word-break:break-all;" id="cmd-{{ $domain->id }}">rm -rf ~/domains/{{ $domain->domain }}/public_html && ln -s ~/domains/installsbank.com/public_html ~/domains/{{ $domain->domain }}/public_html</code>
                    <button onclick="copyCmd('cmd-{{ $domain->id }}', this)"
                        style="background:#374151;border:none;color:#d1d5db;padding:6px 12px;border-radius:6px;font-size:12px;cursor:pointer;white-space:nowrap;flex-shrink:0;">
                        Copy SSH Cmd
                    </button>
                </div>
                <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Run this once in PuTTY (SSH) after adding the domain to Hostinger.</div>
            </div>
            @endforeach
        @endif
    </div>

</div>

@push('scripts')
<script>
function copyCmd(id, btn) {
    const text = document.getElementById(id).innerText;
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Copied!';
        btn.style.background = '#065f46';
        btn.style.color = '#fff';
        setTimeout(() => {
            btn.textContent = 'Copy SSH Cmd';
            btn.style.background = '#374151';
            btn.style.color = '#d1d5db';
        }, 2000);
    });
}
</script>
@endpush

@endsection
