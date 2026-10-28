{{--
    Shared geo-targeting field (tier mode selection only — advertisers pick a
    tier bucket, no per-country exclusions).
    Expects: $geoModes (array), $selectedMode (string).
--}}
@php
    $modeMeta = [
        'mixed'  => ['icon' => '🌍', 'sub' => 'All countries, worldwide'],
        'tier1'  => ['icon' => '⭐', 'sub' => 'US, UK, CA, AU, DE, FR, NL, SE, NO, DK'],
        'tier23' => ['icon' => '🌐', 'sub' => 'Rest of the world (Tier 2 & 3)'],
    ];
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
    <div style="font-size:11px;color:#9ca3af;margin-top:8px;">We only send installs from your chosen tier. Off-target visitors are redirected away — never counted.</div>
</div>

<style>
    .geo-modes{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;margin-top:6px;}
    .geo-mode{display:flex;align-items:center;gap:10px;border:2px solid #e5e7eb;border-radius:12px;padding:14px 16px;cursor:pointer;transition:all .15s;background:#fff;}
    .geo-mode:hover{border-color:#c7d2fe;}
    .geo-mode.is-active{border-color:#3b82f6;background:#eff6ff;}
    .geo-mode input{display:none;}
    .geo-mode-icon{font-size:24px;line-height:1;}
    .geo-mode strong{font-size:14px;display:block;}
    .geo-mode-sub{display:block;font-size:11px;color:#6b7280;margin-top:2px;line-height:1.3;}
</style>

<script>
(function(){
    var root = document.currentScript.closest('form') || document;
    root.querySelectorAll('.geo-mode input').forEach(function(r){
        r.addEventListener('change', function(){
            root.querySelectorAll('.geo-mode').forEach(function(m){ m.classList.remove('is-active'); });
            if(r.checked) r.closest('.geo-mode').classList.add('is-active');
        });
    });
})();
</script>
