@extends('layouts.admin')
@section('title', 'Report Preview')
@section('page-title', 'Adjust & Generate Report')

@section('content')
<div style="max-width:1000px;">

    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('admin.traffic-reports.create') }}" class="btn btn-ghost btn-sm">← New Report</a>
        <span style="font-size:13px;color:#6b7280;">
            {{ $meta['target'] }} · {{ \Carbon\Carbon::parse($meta['date_from'])->format('M d, Y') }} – {{ \Carbon\Carbon::parse($meta['date_to'])->format('M d, Y') }}
            · {{ ucfirst($meta['click_type']) }} clicks
        </span>
    </div>

    <div style="background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#1e40af;">
        Edit any number below. Change the <strong>Total Clicks</strong> and the OS &amp; Geo breakdowns rescale automatically. Edit a single OS or country and the other breakdown re-balances so everything always sums to the total.
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="reportGrid">

        {{-- Total --}}
        <div class="card" style="grid-column:1 / -1;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;">
                <div style="font-size:13px;font-weight:700;color:#374151;margin-bottom:6px;">Total Clicks</div>
                <input type="number" id="totalInput" min="0" value="{{ $total }}"
                       style="font-size:30px;font-weight:900;color:#01BF63;border:2px solid #e5e7eb;border-radius:12px;padding:8px 16px;width:100%;max-width:280px;outline:none;">
            </div>
            <div style="font-size:12px;color:#9ca3af;max-width:280px;">Original fetched total: <strong>{{ number_format($total) }}</strong>. Changing this scales both breakdowns proportionally.</div>
        </div>

        {{-- OS --}}
        <div class="card">
            <div class="card-title mb-4">Clicks by Operating System</div>
            <table style="width:100%;">
                <thead><tr>
                    <th style="text-align:left;">OS</th>
                    <th style="text-align:right;width:120px;">Clicks</th>
                    <th style="text-align:right;width:60px;">%</th>
                </tr></thead>
                <tbody id="osBody"></tbody>
                <tfoot><tr style="border-top:2px solid #e5e7eb;">
                    <td style="padding:10px 0;font-weight:800;">Total</td>
                    <td style="text-align:right;font-weight:800;" id="osSum">0</td>
                    <td style="text-align:right;font-weight:700;color:#9ca3af;">100%</td>
                </tr></tfoot>
            </table>
        </div>

        {{-- Geo --}}
        <div class="card">
            <div class="card-title mb-4">Clicks by Country</div>
            <div style="max-height:520px;overflow-y:auto;">
            <table style="width:100%;">
                <thead><tr>
                    <th style="text-align:left;">Country</th>
                    <th style="text-align:right;width:120px;">Clicks</th>
                    <th style="text-align:right;width:60px;">%</th>
                </tr></thead>
                <tbody id="geoBody"></tbody>
                <tfoot><tr style="border-top:2px solid #e5e7eb;">
                    <td style="padding:10px 0;font-weight:800;">Total</td>
                    <td style="text-align:right;font-weight:800;" id="geoSum">0</td>
                    <td style="text-align:right;font-weight:700;color:#9ca3af;">100%</td>
                </tr></tfoot>
            </table>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.traffic-reports.generate') }}" target="_blank" style="margin-top:24px;" id="genForm">
        @csrf
        <input type="hidden" name="payload" id="payload">
        <button type="submit" class="btn btn-primary" style="padding:12px 26px;font-size:15px;">
            Generate PDF Report →
        </button>
        <span style="margin-left:12px;font-size:12px;color:#9ca3af;">Opens the print-ready report in a new tab.</span>
    </form>
</div>
@endsection

@push('styles')
<style>@media(max-width:820px){ .reportGrid { grid-template-columns:1fr !important; } }</style>
@endpush

@push('scripts')
<script>
const META = @json($meta);
let total  = {{ $total }};
let os  = @json($os);   // [{label, value}]
let geo = @json($geo);  // [{code, label, value}]

// Largest-remainder proportional scaling so a category sums exactly to target
function scaleCategory(arr, target) {
    if (!arr.length) return;
    if (target <= 0) { arr.forEach(x => x.value = 0); return; }
    const cur = arr.reduce((a, b) => a + b.value, 0);
    if (cur <= 0) {
        const base = Math.floor(target / arr.length);
        let rem = target - base * arr.length;
        arr.forEach((x, i) => x.value = base + (i < rem ? 1 : 0));
        return;
    }
    const floors = [], rema = [];
    let sum = 0;
    arr.forEach(x => {
        const exact = x.value * target / cur;
        const f = Math.floor(exact);
        floors.push(f); rema.push(exact - f); sum += f;
    });
    let left = target - sum;
    const order = rema.map((r, i) => [r, i]).sort((a, b) => b[0] - a[0]);
    for (let k = 0; k < left; k++) floors[order[k % order.length][1]]++;
    arr.forEach((x, i) => x.value = floors[i]);
}

function fmt(n) { return Number(n).toLocaleString(); }
function pct(v) { return total > 0 ? (v / total * 100).toFixed(1) : '0.0'; }

function render() {
    document.getElementById('totalInput').value = total;

    const osBody = document.getElementById('osBody');
    osBody.innerHTML = os.map((r, i) => `
        <tr>
            <td style="padding:8px 0;font-weight:600;">${escapeHtml(r.label)}</td>
            <td style="text-align:right;"><input type="number" min="0" value="${r.value}" data-cat="os" data-i="${i}"
                style="width:110px;text-align:right;border:1px solid #e5e7eb;border-radius:6px;padding:5px 8px;font-weight:700;"></td>
            <td style="text-align:right;color:#6b7280;">${pct(r.value)}%</td>
        </tr>`).join('');

    const geoBody = document.getElementById('geoBody');
    geoBody.innerHTML = geo.map((r, i) => `
        <tr>
            <td style="padding:8px 0;font-weight:600;">
                ${r.code ? `<img src="https://flagcdn.com/20x15/${r.code}.png" style="width:20px;height:15px;border-radius:2px;vertical-align:middle;margin-right:6px;" onerror="this.style.display='none'">` : ''}
                ${escapeHtml(r.label)}
            </td>
            <td style="text-align:right;"><input type="number" min="0" value="${r.value}" data-cat="geo" data-i="${i}"
                style="width:110px;text-align:right;border:1px solid #e5e7eb;border-radius:6px;padding:5px 8px;font-weight:700;"></td>
            <td style="text-align:right;color:#6b7280;">${pct(r.value)}%</td>
        </tr>`).join('');

    document.getElementById('osSum').textContent  = fmt(os.reduce((a, b) => a + b.value, 0));
    document.getElementById('geoSum').textContent = fmt(geo.reduce((a, b) => a + b.value, 0));

    bindInputs();
}

function bindInputs() {
    document.querySelectorAll('#osBody input, #geoBody input').forEach(inp => {
        inp.addEventListener('change', e => {
            const cat = e.target.dataset.cat, i = +e.target.dataset.i;
            const val = Math.max(0, parseInt(e.target.value) || 0);
            if (cat === 'os')  { os[i].value = val;  total = os.reduce((a, b) => a + b.value, 0);  scaleCategory(geo, total); }
            else               { geo[i].value = val; total = geo.reduce((a, b) => a + b.value, 0); scaleCategory(os, total); }
            render();
        });
    });
}

document.getElementById('totalInput').addEventListener('change', e => {
    total = Math.max(0, parseInt(e.target.value) || 0);
    scaleCategory(os, total);
    scaleCategory(geo, total);
    render();
});

document.getElementById('genForm').addEventListener('submit', () => {
    document.getElementById('payload').value = JSON.stringify({ total, os, geo, meta: META });
});

function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

render();
</script>
@endpush
