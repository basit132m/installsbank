@extends('layouts.admin')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<style>
    .settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media(max-width:768px){ .settings-grid { grid-template-columns: 1fr; } }
    .settings-section { background: white; border-radius: 12px; padding: 28px; border: 1px solid #e5e7eb; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
    .section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #f3f4f6; }
    .section-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-title { font-size: 16px; font-weight: 700; color: #111827; }
    .section-subtitle { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-label .optional { font-weight: 400; color: #9ca3af; font-size: 11px; margin-left: 4px; }
    .form-control { width: 100%; padding: 10px 13px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; transition: all 0.15s; color: #111827; background: white; }
    .form-control:focus { border-color: #01BF63; box-shadow: 0 0 0 3px rgba(1,191,99,0.1); }
    select.form-control { cursor: pointer; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .btn { padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: inherit; transition: all 0.15s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary { background: #01BF63; color: white; }
    .btn-primary:hover { background: #00a354; }
    .btn-outline { background: white; color: #374151; border: 1.5px solid #e5e7eb; }
    .btn-outline:hover { border-color: #01BF63; color: #01BF63; }
    .btn-test { background: #dbeafe; color: #1d4ed8; }
    .btn-test:hover { background: #bfdbfe; }
    .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-danger  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .hint { font-size: 12px; color: #9ca3af; margin-top: 5px; }
    .test-row { display: flex; gap: 10px; align-items: flex-end; }
    .test-row .form-group { flex: 1; margin-bottom: 0; }
    .status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-log { background: #fef3c7; color: #92400e; }
    .badge-smtp { background: #d1fae5; color: #065f46; }
    .full-width { grid-column: 1 / -1; }
</style>

@if(session('success'))
    <div class="alert alert-success">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ $errors->first() }}
    </div>
@endif

<!-- Email / SMTP Settings -->
<div class="settings-section" style="margin-bottom:24px;">
    <div class="section-header">
        <div class="section-icon" style="background:#dbeafe;">
            <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <div class="section-title">Email / SMTP Settings</div>
            <div class="section-subtitle">Configure outgoing mail for password resets and notifications</div>
        </div>
        <div style="margin-left:auto;">
            @if($settings['MAIL_MAILER'] === 'smtp')
                <span class="status-badge badge-smtp">
                    <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#01BF63"/></svg>
                    SMTP Active
                </span>
            @else
                <span class="status-badge badge-log">
                    <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#f59e0b"/></svg>
                    Log Mode (no real emails)
                </span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Mail Driver</label>
                <select name="MAIL_MAILER" class="form-control" id="mailerSelect" onchange="toggleSmtpFields()">
                    <option value="smtp"    {{ $settings['MAIL_MAILER'] === 'smtp'    ? 'selected' : '' }}>SMTP</option>
                    <option value="sendmail"{{ $settings['MAIL_MAILER'] === 'sendmail'? 'selected' : '' }}>Sendmail</option>
                    <option value="log"     {{ $settings['MAIL_MAILER'] === 'log'     ? 'selected' : '' }}>Log (testing only)</option>
                </select>
                <div class="hint">Use "SMTP" for real email delivery.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Encryption</label>
                <select name="MAIL_ENCRYPTION" class="form-control">
                    <option value="tls"      {{ $settings['MAIL_ENCRYPTION'] === 'tls'      ? 'selected' : '' }}>TLS (port 587 — recommended)</option>
                    <option value="ssl"      {{ $settings['MAIL_ENCRYPTION'] === 'ssl'      ? 'selected' : '' }}>SSL (port 465)</option>
                    <option value="starttls" {{ $settings['MAIL_ENCRYPTION'] === 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                    <option value=""         {{ $settings['MAIL_ENCRYPTION'] === ''         ? 'selected' : '' }}>None</option>
                </select>
            </div>
        </div>

        <div id="smtpFields">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SMTP Host</label>
                    <input type="text" name="MAIL_HOST" class="form-control" value="{{ $settings['MAIL_HOST'] }}" placeholder="mail.installsbank.com">
                    <div class="hint">Your hosting SMTP server address.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">SMTP Port</label>
                    <input type="number" name="MAIL_PORT" class="form-control" value="{{ $settings['MAIL_PORT'] ?: 587 }}" placeholder="587">
                    <div class="hint">587 for TLS, 465 for SSL.</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">SMTP Username</label>
                    <input type="text" name="MAIL_USERNAME" class="form-control" value="{{ $settings['MAIL_USERNAME'] }}" placeholder="contact@installsbank.com">
                    <div class="hint">Usually your full email address.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">SMTP Password</label>
                    <input type="password" name="MAIL_PASSWORD" class="form-control" value="{{ $settings['MAIL_PASSWORD'] }}" placeholder="••••••••••">
                    <div class="hint">Your email account password.</div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">From Email Address</label>
                <input type="email" name="MAIL_FROM_ADDRESS" class="form-control" value="{{ $settings['MAIL_FROM_ADDRESS'] ?: 'contact@installsbank.com' }}" placeholder="contact@installsbank.com" required>
                <div class="hint">Emails will appear sent from this address.</div>
            </div>
            <div class="form-group">
                <label class="form-label">From Name</label>
                <input type="text" name="MAIL_FROM_NAME" class="form-control" value="{{ $settings['MAIL_FROM_NAME'] ?: 'Installs Bank' }}" placeholder="Installs Bank" required>
            </div>
        </div>

        <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Email Settings
            </button>
        </div>
    </form>
</div>

<!-- IMAP / Incoming Email Settings -->
<div class="settings-section" style="margin-bottom:24px;">
    <div class="section-header">
        <div class="section-icon" style="background:#fef3c7;">
            <svg width="20" height="20" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        </div>
        <div>
            <div class="section-title">IMAP / Incoming Email Settings</div>
            <div class="section-subtitle">Used to fetch replies to broadcast emails. Runs automatically every 5 minutes.</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.imap') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">IMAP Host</label>
                <input type="text" name="IMAP_HOST" class="form-control"
                       value="{{ $settings['IMAP_HOST'] ?? 'imap.hostinger.com' }}"
                       placeholder="imap.hostinger.com">
                <div class="hint">Usually imap.hostinger.com for Hostinger accounts.</div>
            </div>
            <div class="form-group">
                <label class="form-label">IMAP Port</label>
                <input type="number" name="IMAP_PORT" class="form-control"
                       value="{{ $settings['IMAP_PORT'] ?? 993 }}"
                       placeholder="993">
                <div class="hint">993 for SSL (recommended).</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">IMAP Username</label>
                <input type="text" name="IMAP_USERNAME" class="form-control"
                       value="{{ $settings['IMAP_USERNAME'] ?? ($settings['MAIL_USERNAME'] ?? '') }}"
                       placeholder="contact@installsbank.com">
                <div class="hint">Usually the same as your SMTP username.</div>
            </div>
            <div class="form-group">
                <label class="form-label">IMAP Password</label>
                <input type="password" name="IMAP_PASSWORD" class="form-control"
                       value="{{ $settings['IMAP_PASSWORD'] ?? ($settings['MAIL_PASSWORD'] ?? '') }}"
                       placeholder="••••••••••">
                <div class="hint">Usually the same as your SMTP password.</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Inbox Folder</label>
                <input type="text" name="IMAP_FOLDER" class="form-control"
                       value="{{ $settings['IMAP_FOLDER'] ?? 'INBOX' }}"
                       placeholder="INBOX">
                <div class="hint">Leave as INBOX unless you use a custom folder.</div>
            </div>
            <div class="form-group"></div>
        </div>

        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save IMAP Settings
        </button>
    </form>
</div>

<!-- Test Email -->
<div class="settings-section" style="margin-bottom:24px;">
    <div class="section-header">
        <div class="section-icon" style="background:#ede9fe;">
            <svg width="20" height="20" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="section-title">Send Test Email</div>
            <div class="section-subtitle">Verify your SMTP settings are working correctly</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.test-email') }}">
        @csrf
        <div class="test-row">
            <div class="form-group">
                <label class="form-label">Send test to</label>
                <input type="email" name="test_email" class="form-control" placeholder="your@email.com" value="{{ auth()->user()->email }}">
            </div>
            <button type="submit" class="btn btn-test" style="margin-bottom:0;white-space:nowrap;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send Test Email
            </button>
        </div>
    </form>
</div>

<!-- Withdrawal Days -->
<div class="settings-section" style="margin-bottom:24px;">
    <div class="section-header">
        <div class="section-icon" style="background:#fce7f3;">
            <svg width="20" height="20" fill="none" stroke="#ec4899" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <div class="section-title">Withdrawal Days</div>
            <div class="section-subtitle">Choose which days publishers can submit withdrawal requests</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.withdrawal-days') }}">
        @csrf

        @php
            $dayNames = [0=>'Sunday', 1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday'];
            $dayColors = [0=>'#f59e0b', 1=>'#3b82f6', 2=>'#3b82f6', 3=>'#3b82f6', 4=>'#3b82f6', 5=>'#3b82f6', 6=>'#f59e0b'];
        @endphp

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
            @foreach($dayNames as $num => $name)
            @php $checked = in_array($num, $withdrawalDays ?? []); @endphp
            <label style="cursor:pointer;">
                <input type="checkbox" name="withdrawal_days[]" value="{{ $num }}"
                       id="day{{ $num }}" style="display:none;" {{ $checked ? 'checked' : '' }}
                       onchange="updateDayCard(this)">
                <div id="daycard{{ $num }}" onclick="document.getElementById('day{{ $num }}').click();updateDayCard(document.getElementById('day{{ $num }}')); return false;"
                     style="padding:12px 18px;border-radius:10px;font-size:14px;font-weight:700;border:2px solid;transition:all 0.15s;user-select:none;
                     {{ $checked ? 'background:#e6faf2;border-color:#01BF63;color:#065f46;' : 'background:#f9fafb;border-color:#e5e7eb;color:#9ca3af;' }}">
                    {{ $name }}
                </div>
            </label>
            @endforeach
        </div>

        <div style="background:#f9fafb;border-radius:8px;padding:12px 16px;font-size:13px;color:#6b7280;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
            <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Times are checked in <strong style="color:#374151;">USA Eastern Time</strong>. If no days are selected, withdrawals will be disabled for all publishers.
        </div>

        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save Withdrawal Days
        </button>
    </form>
</div>

<!-- App Settings -->
<div class="settings-section">
    <div class="section-header">
        <div class="section-icon" style="background:#f0fdf4;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <div class="section-title">Application Settings</div>
            <div class="section-subtitle">Basic app name and URL configuration</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        {{-- Pass mail fields as hidden so validation doesn't strip them --}}
        <input type="hidden" name="MAIL_MAILER"       value="{{ $settings['MAIL_MAILER'] }}">
        <input type="hidden" name="MAIL_FROM_ADDRESS" value="{{ $settings['MAIL_FROM_ADDRESS'] }}">
        <input type="hidden" name="MAIL_FROM_NAME"    value="{{ $settings['MAIL_FROM_NAME'] }}">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Application Name</label>
                <input type="text" name="APP_NAME" class="form-control" value="{{ $settings['APP_NAME'] }}" placeholder="Installs Bank">
            </div>
            <div class="form-group">
                <label class="form-label">Application URL</label>
                <input type="text" name="APP_URL" class="form-control" value="{{ $settings['APP_URL'] }}" placeholder="https://installsbank.com">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save App Settings
        </button>
    </form>
</div>

<!-- Contact Info -->
<div class="settings-section" style="margin-top:24px;">
    <div class="section-header">
        <div class="section-icon" style="background:#f0fdf4;">
            <svg width="20" height="20" fill="none" stroke="#01BF63" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
        <div>
            <div class="section-title">Contact Information</div>
            <div class="section-subtitle">WhatsApp, Telegram and support email shown to publishers</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.contact-info') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">
                    <span style="display:inline-flex;align-items:center;gap:6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.99 0C5.388 0 0 5.388 0 12c0 2.118.555 4.103 1.522 5.825L.058 24l6.304-1.654A11.934 11.934 0 0011.99 24C18.61 24 24 18.612 24 12S18.61 0 11.99 0zm.01 21.818c-1.794 0-3.463-.48-4.897-1.314l-.351-.209-3.642.955.972-3.545-.228-.364A9.799 9.799 0 012.182 12C2.182 6.591 6.591 2.182 12 2.182S21.818 6.591 21.818 12 17.409 21.818 12 21.818z"/></svg>
                        WhatsApp Number
                    </span>
                </label>
                <input type="text" name="whatsapp" class="form-control"
                       value="{{ $contactInfo['whatsapp'] }}"
                       placeholder="+1234567890">
                <div class="hint">Include country code, e.g. +44 7911 123456</div>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <span style="display:inline-flex;align-items:center;gap:6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#26A5E4"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        Telegram Username
                    </span>
                </label>
                <div style="display:flex;align-items:center;gap:0;">
                    <span style="padding:10px 12px;background:#f3f4f6;border:1.5px solid #e5e7eb;border-right:none;border-radius:8px 0 0 8px;font-size:14px;color:#6b7280;font-weight:600;">@</span>
                    <input type="text" name="telegram" class="form-control"
                           value="{{ $contactInfo['telegram'] }}"
                           placeholder="installsbank"
                           style="border-radius:0 8px 8px 0;">
                </div>
                <div class="hint">Username without the @ — e.g. installsbank</div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Support Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ $contactInfo['email'] }}"
                   placeholder="support@installsbank.com"
                   style="max-width:360px;">
            <div class="hint">Displayed to publishers as the support contact email</div>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save Contact Info
        </button>
    </form>
</div>

<!-- Announcements -->
<div class="settings-section" style="margin-top:24px;">
    <div class="section-header">
        <div class="section-icon" style="background:#eff6ff;">
            <svg width="20" height="20" fill="none" stroke="#2563eb" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        </div>
        <div>
            <div class="section-title">Publisher Announcements</div>
            <div class="section-subtitle">Shown on all publisher dashboards until hidden or deleted</div>
        </div>
    </div>

    <!-- Post new announcement -->
    <form method="POST" action="{{ route('admin.announcements.store') }}" style="margin-bottom:24px;">
        @csrf
        <div style="display:grid;grid-template-columns:140px 1fr auto;gap:12px;align-items:flex-start;">
            <div>
                <label class="form-label">Type</label>
                <select name="type" class="form-control">
                    <option value="info">ℹ Info</option>
                    <option value="warning">⚠ Warning</option>
                    <option value="danger">🚨 Danger</option>
                    <option value="success">✓ Success</option>
                </select>
            </div>
            <div>
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="2" required placeholder="Write announcement text..."></textarea>
            </div>
            <div style="padding-top:22px;">
                <button type="submit" class="btn btn-primary">Publish</button>
            </div>
        </div>
    </form>

    <!-- Existing announcements -->
    @forelse($announcements as $ann)
    @php
        $typeColors = [
            'info'    => ['bg'=>'#eff6ff','border'=>'#93c5fd','text'=>'#1e40af'],
            'warning' => ['bg'=>'#fffbeb','border'=>'#fcd34d','text'=>'#92400e'],
            'danger'  => ['bg'=>'#fff1f2','border'=>'#fca5a5','text'=>'#991b1b'],
            'success' => ['bg'=>'#f0fdf4','border'=>'#86efac','text'=>'#166534'],
        ];
        $tc = $typeColors[$ann->type] ?? $typeColors['info'];
    @endphp
    <div style="display:flex;gap:12px;align-items:flex-start;padding:14px 16px;background:{{ $tc['bg'] }};border:1.5px solid {{ $tc['border'] }};border-radius:10px;margin-bottom:10px;{{ !$ann->is_active ? 'opacity:0.5;' : '' }}">
        <div style="flex:1;font-size:13px;color:{{ $tc['text'] }};line-height:1.6;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-right:8px;">{{ $ann->type }}</span>
            {!! nl2br(e($ann->message)) !!}
            <div style="font-size:11px;color:#9ca3af;margin-top:4px;">{{ $ann->created_at->format('M d, Y H:i') }} · {{ $ann->creator->name }}</div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <form method="POST" action="{{ route('admin.announcements.toggle', $ann) }}">
                @csrf
                <button type="submit" class="btn btn-outline" style="padding:5px 12px;font-size:12px;">
                    {{ $ann->is_active ? 'Hide' : 'Show' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}" onsubmit="return confirm('Delete this announcement?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:#fee2e2;color:#991b1b;border:none;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">No announcements yet</div>
    @endforelse
</div>

<script>
function updateDayCard(checkbox) {
    const card = document.getElementById('daycard' + checkbox.value);
    if (checkbox.checked) {
        card.style.background = '#e6faf2';
        card.style.borderColor = '#01BF63';
        card.style.color = '#065f46';
    } else {
        card.style.background = '#f9fafb';
        card.style.borderColor = '#e5e7eb';
        card.style.color = '#9ca3af';
    }
}

function toggleSmtpFields() {
    var mailer = document.getElementById('mailerSelect').value;
    var fields = document.getElementById('smtpFields');
    fields.style.display = (mailer === 'smtp') ? 'block' : 'none';
}
// Run on load
toggleSmtpFields();
</script>
@endsection
