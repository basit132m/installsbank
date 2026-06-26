@extends('layouts.admin')

@section('title', 'Lander Page Manager')
@section('page-title', 'Lander Page Manager')

@section('content')

{{-- Hero strip --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:18px;padding:32px 36px;margin-bottom:28px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;right:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(99,102,241,0.18) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;left:30%;width:160px;height:160px;background:radial-gradient(circle,rgba(16,185,129,0.12) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
        <div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
                <div style="width:46px;height:46px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                </div>
                <h2 style="font-size:22px;font-weight:800;color:#fff;letter-spacing:-0.02em;">Lander Page Manager</h2>
            </div>
            <p style="color:rgba(255,255,255,0.5);font-size:14px;margin-left:58px;">Configure your public download lander page and manage your MEGA URL vault</p>
        </div>
        <a href="/download" target="_blank"
           style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;box-shadow:0 4px 16px rgba(99,102,241,0.35);transition:all 0.2s;"
           onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Preview Lander
        </a>
    </div>

    <div style="display:flex;gap:16px;margin-top:24px;flex-wrap:wrap;">
        <div style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:16px 22px;min-width:140px;">
            <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">MEGA URLs Saved</div>
            <div style="font-size:26px;font-weight:800;color:#fff;">{{ $megaUrls->count() }}</div>
        </div>
        <div style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:16px 22px;min-width:140px;">
            <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Color Scheme</div>
            <div style="font-size:16px;font-weight:700;color:#a5b4fc;text-transform:capitalize;">{{ str_replace('-', ' ', $settings->color_scheme) }}</div>
        </div>
        <div style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:16px 22px;min-width:140px;">
            <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;">Download Count</div>
            <div style="font-size:26px;font-weight:800;color:#6ee7b7;">{{ number_format($settings->download_count) }}</div>
        </div>
    </div>
</div>

{{-- Lander URLs from tracking domains --}}
<div style="background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.04);margin-bottom:24px;">
    <div style="display:flex;align-items:center;gap:12px;padding:18px 24px;border-bottom:1px solid #f3f4f6;">
        <div style="width:36px;height:36px;background:linear-gradient(135deg,#0ea5e9,#6366f1);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
        </div>
        <div>
            <div style="font-size:15px;font-weight:700;color:#111827;">Lander URLs</div>
            <div style="font-size:12px;color:#9ca3af;">Your active tracking domains with the /download path — share any of these</div>
        </div>
    </div>

    @if($trackingDomains->isEmpty())
        <div style="padding:28px 24px;text-align:center;color:#9ca3af;font-size:14px;">
            No active tracking domains found. Add domains in <a href="{{ route('admin.tracking-domains.index') }}" style="color:#6366f1;">Tracking Domains</a>.
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:0;">
            @foreach($trackingDomains as $i => $td)
                @php $url = 'https://' . rtrim($td->domain, '/') . '/download'; @endphp
                <div style="padding:14px 20px;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}display:flex;align-items:center;gap:12px;">
                    <div style="width:8px;height:8px;background:#10b981;border-radius:50%;box-shadow:0 0 6px rgba(16,185,129,0.5);flex-shrink:0;"></div>
                    <div style="flex:1;min-width:0;">
                        @if($td->label)
                            <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:2px;">{{ $td->label }}</div>
                        @endif
                        <div style="font-size:13px;color:#1f2937;font-family:monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" id="lurl-{{ $td->id }}">{{ $url }}</div>
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0;">
                        <button onclick="copyLanderUrl('{{ $url }}', this)"
                                style="padding:5px 12px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;font-weight:600;color:#374151;cursor:pointer;white-space:nowrap;transition:all 0.15s;"
                                onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                            Copy
                        </button>
                        <a href="{{ $url }}" target="_blank"
                           style="padding:5px 10px;background:#ede9fe;border:1px solid #c4b5fd;border-radius:7px;font-size:12px;font-weight:600;color:#6d28d9;text-decoration:none;display:inline-flex;align-items:center;gap:4px;"
                           onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#ede9fe'">
                            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Open
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

    {{-- Left: Settings form --}}
    <div>
        <div style="background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            {{-- Header --}}
            <div style="display:flex;align-items:center;gap:12px;padding:20px 24px;border-bottom:1px solid #f3f4f6;">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">Lander Settings</div>
                    <div style="font-size:12px;color:#9ca3af;">Controls what visitors see on the download page</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.lander.update') }}" style="padding:24px;">
                @csrf

                {{-- Section: Content --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <div style="width:4px;height:18px;background:linear-gradient(180deg,#6366f1,#8b5cf6);border-radius:2px;"></div>
                    <span style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.07em;">Page Content</span>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Page Title</label>
                    <input type="text" name="page_title" value="{{ old('page_title', $settings->page_title) }}"
                           style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#1f2937;outline:none;transition:border-color 0.15s;"
                           onfocus="this.style.borderColor='#6366f1';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.12)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="Your File is Ready" required>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Download Count (displayed number)</label>
                    <input type="number" name="download_count" value="{{ old('download_count', $settings->download_count) }}"
                           style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#1f2937;outline:none;transition:border-color 0.15s;"
                           onfocus="this.style.borderColor='#6366f1';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.12)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           min="0" required>
                </div>

                {{-- Section: MEGA Link --}}
                <div style="display:flex;align-items:center;gap:8px;margin:20px 0 16px;">
                    <div style="width:4px;height:18px;background:linear-gradient(180deg,#10b981,#3b82f6);border-radius:2px;"></div>
                    <span style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.07em;">MEGA Link</span>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Active MEGA URL</label>
                    <textarea name="mega_url" rows="3"
                              style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#1f2937;outline:none;resize:vertical;font-family:monospace;transition:border-color 0.15s;"
                              onfocus="this.style.borderColor='#6366f1';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.12)'"
                              onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                              placeholder="https://mega.nz/file/...">{{ old('mega_url', $settings->mega_url) }}</textarea>
                    <p style="font-size:12px;color:#9ca3af;margin-top:4px;">Paste your MEGA link here. Use the vault below to quickly switch links.</p>
                </div>

                {{-- Section: Password --}}
                <div style="display:flex;align-items:center;gap:8px;margin:20px 0 16px;">
                    <div style="width:4px;height:18px;background:linear-gradient(180deg,#f59e0b,#ef4444);border-radius:2px;"></div>
                    <span style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.07em;">Archive Password</span>
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Password</label>
                    <input type="text" name="archive_password" value="{{ old('archive_password', $settings->archive_password) }}"
                           style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#1f2937;outline:none;font-family:monospace;transition:border-color 0.15s;"
                           onfocus="this.style.borderColor='#6366f1';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.12)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           placeholder="Leave blank if no password">
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#374151;">Show Password on Lander</div>
                        <div style="font-size:12px;color:#9ca3af;">Display the archive password to visitors</div>
                    </div>
                    <label style="position:relative;width:44px;height:24px;flex-shrink:0;">
                        <input type="hidden" name="show_password" value="0">
                        <input type="checkbox" name="show_password" value="1" {{ $settings->show_password ? 'checked' : '' }}
                               style="opacity:0;width:0;height:0;" id="showPw"
                               onchange="this.previousElementSibling.value=this.checked?1:0">
                        <span onclick="document.getElementById('showPw').click()"
                              style="position:absolute;cursor:pointer;inset:0;background:{{ $settings->show_password ? '#6366f1' : '#d1d5db' }};border-radius:24px;transition:0.3s;"
                              id="showPwSlider">
                            <span id="showPwKnob" style="position:absolute;width:18px;height:18px;left:{{ $settings->show_password ? '23px' : '3px' }};bottom:3px;background:white;border-radius:50%;transition:0.3s;"></span>
                        </span>
                    </label>
                </div>

                {{-- Section: Display --}}
                <div style="display:flex;align-items:center;gap:8px;margin:20px 0 16px;">
                    <div style="width:4px;height:18px;background:linear-gradient(180deg,#6366f1,#06b6d4);border-radius:2px;"></div>
                    <span style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.07em;">Display Options</span>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#374151;">Show System Checks</div>
                        <div style="font-size:12px;color:#9ca3af;">Display the virus scan / security check badges</div>
                    </div>
                    <label style="position:relative;width:44px;height:24px;flex-shrink:0;">
                        <input type="hidden" name="show_checks" value="0">
                        <input type="checkbox" name="show_checks" value="1" {{ $settings->show_checks ? 'checked' : '' }}
                               style="opacity:0;width:0;height:0;" id="showChecks"
                               onchange="this.previousElementSibling.value=this.checked?1:0">
                        <span onclick="document.getElementById('showChecks').click()"
                              style="position:absolute;cursor:pointer;inset:0;background:{{ $settings->show_checks ? '#6366f1' : '#d1d5db' }};border-radius:24px;transition:0.3s;"
                              id="showChecksSlider">
                            <span id="showChecksKnob" style="position:absolute;width:18px;height:18px;left:{{ $settings->show_checks ? '23px' : '3px' }};bottom:3px;background:white;border-radius:50%;transition:0.3s;"></span>
                        </span>
                    </label>
                </div>

                {{-- Color scheme --}}
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:10px;">Color Scheme</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                        @php
                            $schemes = [
                                'dark-red'    => ['label'=>'Dark Red',    'color'=>'#dc2626'],
                                'dark-blue'   => ['label'=>'Dark Blue',   'color'=>'#2563eb'],
                                'dark-green'  => ['label'=>'Dark Green',  'color'=>'#059669'],
                                'dark-purple' => ['label'=>'Dark Purple', 'color'=>'#7c3aed'],
                                'neon-cyan'   => ['label'=>'Neon Cyan',   'color'=>'#06b6d4'],
                                'amber-gold'  => ['label'=>'Amber Gold',  'color'=>'#d97706'],
                            ];
                        @endphp
                        @foreach($schemes as $key => $meta)
                            <label style="cursor:pointer;">
                                <input type="radio" name="color_scheme" value="{{ $key }}" {{ $settings->color_scheme === $key ? 'checked' : '' }} style="display:none;" class="scheme-radio" data-key="{{ $key }}">
                                <div class="scheme-card" data-key="{{ $key }}"
                                     style="padding:10px 8px;border-radius:10px;border:2px solid {{ $settings->color_scheme === $key ? $meta['color'] : '#e5e7eb' }};background:{{ $settings->color_scheme === $key ? $meta['color'].'12' : '#f9fafb' }};text-align:center;transition:all 0.15s;">
                                    <div style="width:20px;height:20px;background:{{ $meta['color'] }};border-radius:50%;margin:0 auto 5px;"></div>
                                    <div style="font-size:11px;font-weight:600;color:{{ $settings->color_scheme === $key ? $meta['color'] : '#6b7280' }};">{{ $meta['label'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                        style="width:100%;padding:13px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(99,102,241,0.35);transition:all 0.2s;"
                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                    Save Settings
                </button>
            </form>
        </div>
    </div>

    {{-- Right: URL Vault --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Add to vault --}}
        <div style="background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            <div style="display:flex;align-items:center;gap:12px;padding:20px 24px;border-bottom:1px solid #f3f4f6;">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#10b981,#3b82f6);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">Add to Vault</div>
                    <div style="font-size:12px;color:#9ca3af;">Save a MEGA URL with a nickname for reuse</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.mega-urls.store') }}" style="padding:20px;">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Nickname</label>
                    <input type="text" name="nickname" placeholder="e.g. v2.1 Release June"
                           style="width:100%;padding:9px 13px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#1f2937;outline:none;transition:border-color 0.15s;"
                           onfocus="this.style.borderColor='#10b981';this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.12)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           required>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">MEGA URL</label>
                    <input type="text" name="url" placeholder="https://mega.nz/file/..."
                           style="width:100%;padding:9px 13px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#1f2937;outline:none;font-family:monospace;transition:border-color 0.15s;"
                           onfocus="this.style.borderColor='#10b981';this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.12)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'"
                           required>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Notes <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>
                    <input type="text" name="notes" placeholder="e.g. Windows 10/11 compatible"
                           style="width:100%;padding:9px 13px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#1f2937;outline:none;transition:border-color 0.15s;"
                           onfocus="this.style.borderColor='#10b981';this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.12)'"
                           onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                </div>
                <button type="submit"
                        style="width:100%;padding:11px;background:linear-gradient(135deg,#10b981,#3b82f6);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 3px 12px rgba(16,185,129,0.3);transition:all 0.2s;"
                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                    Save to Vault
                </button>
            </form>
        </div>

        {{-- Vault list --}}
        <div style="background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
            <div style="display:flex;align-items:center;gap:12px;padding:20px 24px;border-bottom:1px solid #f3f4f6;">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#f59e0b,#ef4444);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:#111827;">MEGA URL Vault</div>
                    <div style="font-size:12px;color:#9ca3af;">{{ $megaUrls->count() }} saved {{ Str::plural('link', $megaUrls->count()) }} &mdash; click "Use" to activate</div>
                </div>
            </div>

            @if($megaUrls->isEmpty())
                <div style="padding:40px 24px;text-align:center;">
                    <div style="width:52px;height:52px;background:#f3f4f6;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <svg width="24" height="24" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <p style="font-size:14px;color:#9ca3af;font-weight:500;">No URLs in vault yet</p>
                    <p style="font-size:13px;color:#d1d5db;margin-top:4px;">Add your first MEGA link above</p>
                </div>
            @else
                <div>
                    @foreach($megaUrls as $mu)
                        @php $isActive = trim($settings->mega_url) === trim($mu->url); @endphp
                        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;transition:background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                {{-- Indicator --}}
                                <div style="margin-top:3px;width:8px;height:8px;border-radius:50%;background:{{ $isActive ? '#10b981' : '#d1d5db' }};box-shadow:{{ $isActive ? '0 0 8px rgba(16,185,129,0.6)' : 'none' }};flex-shrink:0;"></div>

                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
                                        <span style="font-size:14px;font-weight:700;color:#111827;">{{ $mu->nickname }}</span>
                                        @if($isActive)
                                            <span style="display:inline-flex;align-items:center;padding:2px 8px;background:#d1fae5;color:#065f46;border-radius:20px;font-size:11px;font-weight:700;">Active</span>
                                        @endif
                                    </div>
                                    <div style="font-size:12px;color:#9ca3af;font-family:monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:260px;" title="{{ $mu->url }}">{{ $mu->url }}</div>
                                    @if($mu->notes)
                                        <div style="font-size:12px;color:#6b7280;margin-top:3px;">{{ $mu->notes }}</div>
                                    @endif
                                    <div style="font-size:11px;color:#d1d5db;margin-top:3px;">Added {{ $mu->created_at->diffForHumans() }}</div>
                                </div>

                                <div style="display:flex;gap:6px;flex-shrink:0;align-items:center;">
                                    @if(!$isActive)
                                        <form method="POST" action="{{ route('admin.mega-urls.use', $mu) }}">
                                            @csrf
                                            <button type="submit"
                                                    style="padding:6px 12px;background:linear-gradient(135deg,#10b981,#3b82f6);color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;">
                                                Use
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.mega-urls.destroy', $mu) }}"
                                          onsubmit="return confirm('Remove this URL from vault?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                style="width:30px;height:30px;background:#fee2e2;color:#dc2626;border:none;border-radius:7px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
// Copy lander URL
function copyLanderUrl(url, btn) {
    navigator.clipboard.writeText(url).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = url;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    }).finally ? null : null;
    const orig = btn.textContent;
    btn.textContent = 'Copied!';
    btn.style.background = '#d1fae5';
    btn.style.borderColor = '#6ee7b7';
    btn.style.color = '#065f46';
    setTimeout(() => {
        btn.textContent = orig;
        btn.style.background = '#f3f4f6';
        btn.style.borderColor = '#e5e7eb';
        btn.style.color = '#374151';
    }, 2000);
}

// Toggle visual update
function wireToggle(checkboxId, sliderId, knobId, activeColor) {
    const cb = document.getElementById(checkboxId);
    const slider = document.getElementById(sliderId);
    const knob = document.getElementById(knobId);
    if (!cb) return;
    cb.addEventListener('change', function() {
        slider.style.background = this.checked ? activeColor : '#d1d5db';
        knob.style.left = this.checked ? '23px' : '3px';
    });
}
wireToggle('showPw', 'showPwSlider', 'showPwKnob', '#6366f1');
wireToggle('showChecks', 'showChecksSlider', 'showChecksKnob', '#6366f1');

// Color scheme picker
const schemeColors = {
    'dark-red':    '#dc2626',
    'dark-blue':   '#2563eb',
    'dark-green':  '#059669',
    'dark-purple': '#7c3aed',
    'neon-cyan':   '#06b6d4',
    'amber-gold':  '#d97706',
};
document.querySelectorAll('.scheme-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.scheme-card').forEach(card => {
            const k = card.dataset.key;
            const c = schemeColors[k];
            card.style.borderColor = '#e5e7eb';
            card.style.background = '#f9fafb';
            card.querySelector('div:last-child').style.color = '#6b7280';
        });
        const active = document.querySelector(`.scheme-card[data-key="${this.dataset.key}"]`);
        const ac = schemeColors[this.dataset.key];
        active.style.borderColor = ac;
        active.style.background = ac + '12';
        active.querySelector('div:last-child').style.color = ac;
    });
});
</script>
@endpush
