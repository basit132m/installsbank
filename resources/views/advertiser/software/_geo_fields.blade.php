{{--
    Shared geo-targeting fields (mode cards + flag-based country exclusion picker).
    Expects: $geoModes (array), $countryGroups (array), $selectedMode (string),
             $selectedExcluded (array of ISO codes).
--}}
@php
    $modeMeta = [
        'mixed'  => ['icon' => '🌍', 'sub' => 'All countries, worldwide'],
        'tier1'  => ['icon' => '⭐', 'sub' => 'US, UK, CA, AU, DE, FR, NL, SE, NO, DK'],
        'tier23' => ['icon' => '🌐', 'sub' => 'Rest of the world (Tier 2 & 3)'],
    ];
    $exclCount = count($selectedExcluded);
@endphp

<div class="form-group">
    <label class="form-label">Target Countries (Geo) *</label>
    <div class="geo-modes">
        @foreach($geoModes as $val => $label)
        <label class="geo-mode {{ $selectedMode === $val ? 'is-active' : '' }}">
            <input type="radio" name="geo_mode" value="{{ $val }}" {{ $selectedMode === $val ? 'checked' : '' }}>
            <span class="geo-mode-icon">{{ $modeMeta[$val]['icon'] ?? '🌍' }}</span>
            <span>
                <strong>{{ $label }}</strong>
                <span class="geo-mode-sub">{{ $modeMeta[$val]['sub'] ?? '' }}</span>
            </span>
        </label>
        @endforeach
    </div>
    <div style="font-size:11px;color:#9ca3af;margin-top:6px;">We only send installs from your chosen tier. Off-target visitors are redirected away — never counted.</div>
</div>

<div class="form-group">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:8px;">
        <label class="form-label" style="margin:0;">Exclude specific countries</label>
        <span class="geo-excl-count" style="font-size:12px;color:#6b7280;">{{ $exclCount }} excluded</span>
    </div>
    <input type="text" class="form-control geo-search" placeholder="Search a country to exclude…" style="margin-bottom:10px;" autocomplete="off">

    <div class="geo-grid-wrap">
        @foreach($countryGroups as $group => $list)
        <div class="geo-group-title">{{ $group }}</div>
        <div class="geo-grid">
            @foreach($list as $code => $cname)
            <label class="geo-chip {{ in_array($code, $selectedExcluded) ? 'is-excluded' : '' }}" data-search="{{ strtolower($cname.' '.$code) }}">
                <input type="checkbox" name="excluded_countries[]" value="{{ $code }}" {{ in_array($code, $selectedExcluded) ? 'checked' : '' }}>
                <img class="geo-flag" src="https://flagcdn.com/24x18/{{ strtolower($code) }}.png" srcset="https://flagcdn.com/48x36/{{ strtolower($code) }}.png 2x" width="24" height="18" alt="" loading="lazy">
                <span class="geo-chip-name">{{ $cname }}</span>
                <span class="geo-chip-code">{{ $code }}</span>
            </label>
            @endforeach
        </div>
        @endforeach
    </div>
    <div style="font-size:11px;color:#9ca3af;margin-top:8px;">Tap a country to exclude it (turns red). Example: pick <strong>Tier 1 only</strong> above and exclude <strong>Canada</strong> to get Tier 1 without Canada.</div>
</div>

<style>
    .geo-modes{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;margin-top:6px;}
    .geo-mode{display:flex;align-items:center;gap:10px;border:2px solid #e5e7eb;border-radius:12px;padding:12px 14px;cursor:pointer;transition:all .15s;background:#fff;}
    .geo-mode:hover{border-color:#c7d2fe;}
    .geo-mode.is-active{border-color:#3b82f6;background:#eff6ff;}
    .geo-mode input{display:none;}
    .geo-mode-icon{font-size:22px;line-height:1;}
    .geo-mode strong{font-size:13px;display:block;}
    .geo-mode-sub{display:block;font-size:11px;color:#6b7280;margin-top:2px;line-height:1.3;}
    .geo-grid-wrap{max-height:320px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:12px;padding:12px;background:#fafafa;}
    .geo-group-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;margin:4px 0 8px;}
    .geo-group-title:not(:first-child){margin-top:14px;}
    .geo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;}
    .geo-chip{display:flex;align-items:center;gap:8px;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 10px;cursor:pointer;background:#fff;transition:all .12s;user-select:none;}
    .geo-chip:hover{border-color:#cbd5e1;}
    .geo-chip input{display:none;}
    .geo-flag{border-radius:3px;box-shadow:0 0 0 1px rgba(0,0,0,.06);flex-shrink:0;}
    .geo-chip-name{font-size:13px;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .geo-chip-code{font-size:11px;color:#9ca3af;font-family:monospace;}
    .geo-chip.is-excluded{border-color:#ef4444;background:#fef2f2;}
    .geo-chip.is-excluded .geo-chip-name{color:#b91c1c;font-weight:600;}
    .geo-chip.is-excluded::after{content:"✕ excluded";font-size:10px;color:#ef4444;font-weight:700;margin-left:auto;}
    .geo-chip.is-excluded .geo-chip-code{display:none;}
    .geo-hidden{display:none !important;}
</style>

<script>
(function(){
    var root = document.currentScript.closest('form') || document;
    // Toggle excluded state + keep the count in sync.
    function refreshCount(){
        var n = root.querySelectorAll('.geo-chip input:checked').length;
        var el = root.querySelector('.geo-excl-count');
        if(el) el.textContent = n + ' excluded';
    }
    root.querySelectorAll('.geo-chip').forEach(function(chip){
        var cb = chip.querySelector('input');
        chip.addEventListener('click', function(e){
            // let the native checkbox toggle, then reflect state
            setTimeout(function(){ chip.classList.toggle('is-excluded', cb.checked); refreshCount(); }, 0);
        });
    });
    // Active mode card highlight.
    root.querySelectorAll('.geo-mode input').forEach(function(r){
        r.addEventListener('change', function(){
            root.querySelectorAll('.geo-mode').forEach(function(m){ m.classList.remove('is-active'); });
            if(r.checked) r.closest('.geo-mode').classList.add('is-active');
        });
    });
    // Search filter.
    var search = root.querySelector('.geo-search');
    if(search){
        search.addEventListener('input', function(){
            var q = search.value.trim().toLowerCase();
            root.querySelectorAll('.geo-chip').forEach(function(chip){
                chip.classList.toggle('geo-hidden', q !== '' && chip.dataset.search.indexOf(q) === -1);
            });
            // Hide a group title if all its chips are hidden.
            root.querySelectorAll('.geo-group-title').forEach(function(title){
                var grid = title.nextElementSibling;
                var anyVisible = grid && grid.querySelector('.geo-chip:not(.geo-hidden)');
                title.classList.toggle('geo-hidden', !anyVisible);
            });
        });
    }
})();
</script>
