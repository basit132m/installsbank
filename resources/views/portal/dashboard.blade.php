<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $account->display_title ?: 'Analytics Dashboard' }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='7' fill='%230f172a'/><path d='M8 20l4-5 3 3 5-7' stroke='%2338bdf8' stroke-width='2.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f1f5f9; color:#0f172a; }
        .topbar { background:#0f172a; color:#fff; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
        .brand { display:flex; align-items:center; gap:12px; }
        .brand .ico { width:38px; height:38px; border-radius:10px; background:rgba(56,189,248,.15); display:flex; align-items:center; justify-content:center; }
        .brand .t { font-size:16px; font-weight:800; }
        .brand .s { font-size:12px; color:#94a3b8; }
        .logout { background:rgba(255,255,255,.08); color:#e2e8f0; border:1px solid rgba(255,255,255,.15); border-radius:9px; padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit; }
        .wrap { max-width:1040px; margin:0 auto; padding:26px 24px; }

        .idcard { background:linear-gradient(135deg,#0f172a,#1e293b); color:#fff; border-radius:16px; padding:20px 24px; margin-bottom:22px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; }
        .idcard .l { font-size:12px; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
        .idcard .code { font-size:22px; font-weight:900; font-family:monospace; letter-spacing:1px; margin-top:4px; }

        .grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:22px; }
        .stat { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; }
        .stat .v { font-size:30px; font-weight:900; color:#0f172a; letter-spacing:-1px; }
        .stat .k { font-size:12px; color:#64748b; margin-top:4px; font-weight:600; }
        .card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:22px; margin-bottom:22px; }
        .card h2 { font-size:15px; font-weight:800; margin-bottom:2px; }
        .card .sub { font-size:12px; color:#94a3b8; margin-bottom:14px; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8; padding:9px 8px; border-bottom:1.5px solid #e2e8f0; }
        td { padding:10px 8px; font-size:14px; border-bottom:1px solid #f1f5f9; }
        .flag { width:22px; height:16px; border-radius:2px; object-fit:cover; vertical-align:middle; margin-right:8px; }
        @media(max-width:800px){ .grid { grid-template-columns:1fr 1fr; } }
        @media(max-width:480px){ .grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="brand">
            <div class="ico">
                <svg width="20" height="20" fill="none" stroke="#38bdf8" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <div class="t">{{ $account->display_title ?: 'Analytics Dashboard' }}</div>
                <div class="s">Traffic Statistics</div>
            </div>
        </div>
        <form method="POST" action="{{ route('portal.logout') }}">@csrf
            <button class="logout" type="submit">Sign Out</button>
        </form>
    </div>

    <div class="wrap">
        {{-- Tracking ID --}}
        <div class="idcard">
            <div>
                <div class="l">Your Tracking ID</div>
                <div class="code">{{ $account->trackingLink?->unique_code ?? '—' }}</div>
            </div>
            <div style="text-align:right;font-size:12px;color:#94a3b8;">
                Windows traffic only<br>Updated live
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid">
            <div class="stat"><div class="v">{{ number_format($data['today']) }}</div><div class="k">Clicks Today</div></div>
            <div class="stat"><div class="v">{{ number_format($data['week']) }}</div><div class="k">This Week</div></div>
            <div class="stat"><div class="v">{{ number_format($data['month']) }}</div><div class="k">This Month</div></div>
            <div class="stat"><div class="v">{{ number_format($data['all_time']) }}</div><div class="k">All Time</div></div>
        </div>

        {{-- Chart --}}
        <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
                <div>
                    <h2 style="margin:0;">Clicks</h2>
                    <div class="sub" style="margin:2px 0 0;">Windows clicks · {{ $chart['title'] }}</div>
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @foreach(['today'=>'Today','yesterday'=>'Yesterday','7days'=>'Last 7 Days'] as $key => $lbl)
                    <a href="?period={{ $key }}"
                       style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;{{ $period === $key ? 'background:#0ea5e9;color:#fff;' : 'background:#f1f5f9;color:#64748b;' }}">{{ $lbl }}</a>
                    @endforeach
                </div>
            </div>
            <div id="chart"></div>
        </div>

        {{-- Filtered breakdown --}}
        <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
                <div>
                    <h2 style="margin:0;">Clicks by Country</h2>
                    <div class="sub" style="margin:2px 0 0;">Windows clicks · {{ $periodData['label'] }} · <strong style="color:#0f172a;">{{ number_format($periodData['total']) }}</strong> total</div>
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    @foreach(['today'=>'Today','yesterday'=>'Yesterday','7days'=>'Last 7 Days'] as $key => $lbl)
                    <a href="?period={{ $key }}"
                       style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;{{ $period === $key ? 'background:#0ea5e9;color:#fff;' : 'background:#f1f5f9;color:#64748b;' }}">{{ $lbl }}</a>
                    @endforeach
                </div>
            </div>
            @if(count($periodData['countries']) > 0)
            <div style="max-height:420px;overflow-y:auto;">
                <table>
                    <thead><tr><th>Country</th><th style="text-align:right;">Clicks</th></tr></thead>
                    <tbody>
                        @foreach($periodData['countries'] as $c)
                        <tr>
                            <td>
                                @if(!empty($c['code']))<img class="flag" src="https://flagcdn.com/32x24/{{ $c['code'] }}.png" onerror="this.style.display='none'">@endif
                                {{ $c['name'] ?: 'Unknown' }}
                            </td>
                            <td style="text-align:right;font-weight:700;">{{ number_format($c['value']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="text-align:center;padding:30px;color:#94a3b8;font-size:13px;">No clicks in this period.</div>
            @endif
        </div>
    </div>

    <script>
    const chartData = @json($chart['points']);
    new ApexCharts(document.getElementById('chart'), {
        series: [{ name: 'Clicks', data: chartData.map(d => d.clicks) }],
        chart: { type: 'area', height: 260, toolbar: { show: false }, fontFamily: 'inherit' },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
        colors: ['#0ea5e9'],
        xaxis: { categories: chartData.map(d => d.label), labels: { style: { fontSize: '11px' }, rotate: -45, rotateAlways: false } },
        dataLabels: { enabled: false },
        grid: { borderColor: '#f1f5f9' },
        tooltip: { y: { formatter: v => v.toLocaleString() + ' clicks' } }
    }).render();
    </script>
</body>
</html>
