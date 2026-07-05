<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta['title'] }} — {{ $reportNo }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root { --ink:#0f172a; --muted:#64748b; --line:#e5e7eb; --green:#01BF63; --accent:#4f46e5; }
        body { font-family:'Inter',sans-serif; background:#f1f5f9; color:var(--ink); }

        .toolbar {
            position:sticky; top:0; z-index:10; background:#0f172a; color:#fff;
            display:flex; align-items:center; justify-content:space-between; gap:12px;
            padding:12px 22px; flex-wrap:wrap;
        }
        .toolbar .t-title { font-size:14px; font-weight:700; }
        .toolbar .t-sub { font-size:12px; color:#94a3b8; }
        .btn-print {
            background:var(--green); color:#fff; border:none; border-radius:9px;
            padding:10px 20px; font-size:14px; font-weight:700; cursor:pointer;
            display:inline-flex; align-items:center; gap:8px;
        }
        .btn-print:hover { background:#00a354; }
        .btn-ghost { background:rgba(255,255,255,.1); color:#e2e8f0; border:1px solid rgba(255,255,255,.15); border-radius:9px; padding:10px 16px; font-size:13px; font-weight:600; text-decoration:none; }

        /* A4 sheet */
        .sheet {
            width:210mm; min-height:297mm; margin:22px auto; background:#fff;
            padding:22mm 20mm; box-shadow:0 8px 40px rgba(0,0,0,.12); position:relative;
        }

        .rp-head { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; border-bottom:3px solid var(--ink); padding-bottom:18px; }
        .rp-logo { height:44px; }
        .rp-head .co { font-size:12px; color:var(--muted); margin-top:6px; line-height:1.6; }
        .rp-badge { text-align:right; }
        .rp-badge .rid { font-family:monospace; font-size:12px; color:var(--muted); }
        .rp-badge .rdate { font-size:12px; color:var(--muted); margin-top:2px; }

        .rp-title { margin-top:26px; text-align:center; }
        .rp-title h1 { font-size:28px; font-weight:900; letter-spacing:-.5px; }
        .rp-meta { display:flex; gap:34px; margin-top:14px; flex-wrap:wrap; justify-content:center; }
        .rp-meta .m { text-align:center; }
        .rp-meta .m .l { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
        .rp-meta .m .v { font-size:14px; font-weight:700; margin-top:2px; }

        .hero {
            margin-top:26px; background:linear-gradient(135deg,#0f172a,#1e293b); color:#fff;
            border-radius:16px; padding:26px 30px; display:flex; align-items:center; justify-content:space-between; gap:20px;
        }
        .hero .l { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#94a3b8; }
        .hero .big { font-size:52px; font-weight:900; letter-spacing:-2px; line-height:1; margin-top:6px; }
        .hero .r { text-align:right; font-size:12px; color:#94a3b8; line-height:1.8; }

        .section { margin-top:30px; }
        .section h2 { font-size:15px; font-weight:800; display:flex; align-items:center; gap:8px; margin-bottom:14px; }
        .section h2 .dot { width:10px; height:10px; border-radius:3px; }

        table.data { width:100%; border-collapse:collapse; }
        table.data th { text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); padding:8px 10px; border-bottom:1.5px solid var(--line); }
        table.data td { padding:9px 10px; font-size:13px; border-bottom:1px solid #f1f5f9; }
        table.data td.num { text-align:right; font-weight:700; font-variant-numeric:tabular-nums; }
        table.data td.pct { text-align:right; color:var(--muted); width:70px; }
        .bar { height:6px; background:#f1f5f9; border-radius:4px; overflow:hidden; margin-top:5px; }
        .bar > i { display:block; height:100%; border-radius:4px; }
        table.data tfoot td { border-top:2px solid var(--ink); border-bottom:none; font-weight:900; font-size:14px; padding-top:11px; }
        .flag { width:22px; height:16px; border-radius:2px; vertical-align:middle; margin-right:8px; object-fit:cover; }

        /* Commercial terms */
        .terms { display:flex; gap:14px; flex-wrap:wrap; }
        .terms .term { flex:1; min-width:150px; background:#f8fafc; border:1px solid var(--line); border-radius:12px; padding:14px 16px; }
        .terms .term .tl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
        .terms .term .tv { font-size:18px; font-weight:900; color:var(--ink); margin-top:4px; }

        /* Centered disclaimer note */
        .note-centered { margin-top:30px; text-align:center; font-size:11px; color:var(--muted); line-height:1.8; max-width:520px; margin-left:auto; margin-right:auto; }

        .rp-foot { margin-top:26px; display:flex; align-items:flex-end; justify-content:space-between; gap:20px; border-top:1px solid var(--line); padding-top:22px; }
        .stamp-wrap { text-align:center; }
        .stamp-wrap img { height:120px; object-fit:contain; }
        .stamp-wrap .lbl { font-size:11px; color:var(--muted); font-weight:600; margin-top:4px; }

        /* E-signature */
        .sig-wrap { text-align:center; }
        .esign { font-family:'Dancing Script',cursive; font-size:40px; font-weight:700; color:#1e3a8a; line-height:1; }
        .sig-line { border-top:1.5px solid var(--ink); width:210px; margin:2px auto 0; }
        .sig-name { font-size:13px; font-weight:800; margin-top:6px; }
        .sig-role { font-size:11px; color:var(--muted); margin-top:1px; }

        .watermark {
            position:absolute; top:46%; left:50%; transform:translate(-50%,-50%) rotate(-18deg);
            width:78%; opacity:.05; pointer-events:none; z-index:0;
        }
        .watermark img { width:100%; }
        .sheet > *:not(.watermark) { position:relative; z-index:1; }

        @media print {
            @page { size:A4; margin:0; }
            body { background:#fff; }
            .toolbar { display:none; }
            .sheet { width:auto; min-height:auto; margin:0; box-shadow:none; padding:16mm 15mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <div class="t-title">{{ $meta['title'] }}</div>
            <div class="t-sub">{{ $reportNo }} · Use “Download PDF”, then choose <strong>Save as PDF</strong> as the destination.</div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.traffic-reports.create') }}" class="btn-ghost">← New Report</a>
            <button class="btn-print" onclick="window.print()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Download PDF
            </button>
        </div>
    </div>

    <div class="sheet">
        <div class="watermark">
            <img src="{{ $logoUrl }}" alt="" onerror="this.parentNode.style.display='none'">
        </div>

        {{-- Header --}}
        <div class="rp-head">
            <div>
                <img class="rp-logo" src="{{ $logoUrl }}" alt="Installs Bank"
                     onerror="this.outerHTML='<div style=&quot;font-size:22px;font-weight:900;color:#0f172a&quot;>Installs Bank</div>'">
                <div class="co">Installs Bank · Traffic Analytics<br>installsbank.com</div>
            </div>
            <div class="rp-badge">
                <div class="rid">{{ $reportNo }}</div>
                <div class="rdate">Generated {{ $generatedAt }}</div>
            </div>
        </div>

        {{-- Title + meta --}}
        <div class="rp-title">
            <h1>{{ $meta['title'] }}</h1>
            <div class="rp-meta">
                <div class="m"><div class="l">Reporting Period</div><div class="v">{{ \Carbon\Carbon::parse($meta['date_from'])->format('M d, Y') }} – {{ \Carbon\Carbon::parse($meta['date_to'])->format('M d, Y') }}</div></div>
                <div class="m"><div class="l">Traffic Source</div><div class="v">{{ $meta['target'] }}</div></div>
                @if($meta['prepared_for'])
                <div class="m"><div class="l">Prepared For</div><div class="v">{{ $meta['prepared_for'] }}</div></div>
                @endif
            </div>
        </div>

        {{-- Hero total --}}
        <div class="hero">
            <div>
                <div class="l">Total Clicks</div>
                <div class="big">{{ number_format($total) }}</div>
            </div>
            <div class="r">
                Period total across all sources<br>
                {{ \Carbon\Carbon::parse($meta['date_from'])->diffInDays(\Carbon\Carbon::parse($meta['date_to'])) + 1 }} day(s) reporting window
            </div>
        </div>

        {{-- OS breakdown --}}
        @php $osColors = ['#4f46e5','#01BF63','#0891b2','#d97706','#ef4444','#7c3aed','#ec4899','#14b8a6','#64748b']; @endphp
        <div class="section">
            <h2><span class="dot" style="background:#4f46e5;"></span>Clicks by Operating System</h2>
            <table class="data">
                <thead><tr><th>Operating System</th><th style="text-align:right;">Clicks</th><th style="text-align:right;">Share</th></tr></thead>
                <tbody>
                    @foreach($os as $i => $row)
                    @php $p = $total > 0 ? round($row['value'] / $total * 100, 1) : 0; $c = $osColors[$i % count($osColors)]; @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $row['label'] }}
                            <div class="bar"><i style="width:{{ $p }}%;background:{{ $c }};"></i></div>
                        </td>
                        <td class="num">{{ number_format($row['value']) }}</td>
                        <td class="pct">{{ $p }}%</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot><tr><td>Total</td><td class="num">{{ number_format(collect($os)->sum('value')) }}</td><td class="pct">100%</td></tr></tfoot>
            </table>
        </div>

        {{-- Geo breakdown --}}
        <div class="section">
            <h2><span class="dot" style="background:#01BF63;"></span>Clicks by Country</h2>
            <table class="data">
                <thead><tr><th>Country</th><th style="text-align:right;">Clicks</th><th style="text-align:right;">Share</th></tr></thead>
                <tbody>
                    @foreach($geo as $row)
                    @php $p = $total > 0 ? round($row['value'] / $total * 100, 1) : 0; @endphp
                    <tr>
                        <td style="font-weight:600;">
                            @if($row['code'])<img class="flag" src="https://flagcdn.com/32x24/{{ $row['code'] }}.png" onerror="this.style.display='none'">@endif
                            {{ $row['label'] }}
                        </td>
                        <td class="num">{{ number_format($row['value']) }}</td>
                        <td class="pct">{{ $p }}%</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot><tr><td>Total</td><td class="num">{{ number_format(collect($geo)->sum('value')) }}</td><td class="pct">100%</td></tr></tfoot>
            </table>
        </div>

        {{-- Commercial terms --}}
        @if($meta['rate'] || $meta['payment_terms'] || $meta['tracking_code'])
        <div class="section">
            <h2><span class="dot" style="background:#d97706;"></span>Commercial Terms</h2>
            <div class="terms">
                @if($meta['rate'])
                <div class="term"><div class="tl">Offered Rate</div><div class="tv">${{ $meta['rate'] }} <span style="font-size:12px;font-weight:600;color:var(--muted);">/ day</span></div></div>
                @endif
                @if($meta['payment_terms'])
                <div class="term"><div class="tl">Payment Terms</div><div class="tv">{{ $meta['payment_terms'] }}</div></div>
                @endif
                @if($meta['tracking_code'])
                <div class="term"><div class="tl">Publisher Tracking Code</div><div class="tv" style="font-family:monospace;">{{ $meta['tracking_code'] }}</div></div>
                @endif
            </div>
        </div>
        @endif

        {{-- Centered note --}}
        <div class="note-centered">
            This report summarizes click activity recorded by Installs Bank's tracking system for the stated
            period and source. Figures represent {{ $meta['title'] }} data and are provided for testing and
            verification purposes.
        </div>

        {{-- Signature + stamp --}}
        <div class="rp-foot">
            <div class="sig-wrap">
                <div class="esign">Williams Smith</div>
                <div class="sig-line"></div>
                <div class="sig-name">Williams Smith</div>
                <div class="sig-role">Installs Bank — Chief Executive Officer</div>
            </div>
            <div class="stamp-wrap">
                <img src="{{ $stampUrl }}" alt="Official Stamp" onerror="this.style.display='none';document.getElementById('stampLbl').style.display='none';">
                <div class="lbl" id="stampLbl">Official Stamp</div>
            </div>
        </div>
    </div>
</body>
</html>
