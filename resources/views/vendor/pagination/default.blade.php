@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">

    {{-- Summary --}}
    <div style="font-size:12px;color:#9ca3af;">
        Showing <strong style="color:#374151;">{{ $paginator->firstItem() }}</strong>–<strong style="color:#374151;">{{ $paginator->lastItem() }}</strong> of <strong style="color:#374151;">{{ $paginator->total() }}</strong> results
    </div>

    {{-- Page buttons --}}
    <div style="display:flex;align-items:center;gap:4px;">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:#f9fafb;border:1px solid #e5e7eb;color:#d1d5db;cursor:not-allowed;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:#fff;border:1px solid #e5e7eb;color:#374151;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#d1d5db'"
               onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;font-size:13px;color:#9ca3af;">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,#01BF63,#059669);color:#fff;font-size:13px;font-weight:700;box-shadow:0 2px 8px rgba(1,191,99,.35);">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:#fff;border:1px solid #e5e7eb;color:#374151;font-size:13px;font-weight:500;text-decoration:none;transition:all .15s;"
                           onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#d1d5db'"
                           onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:#fff;border:1px solid #e5e7eb;color:#374151;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.background='#f3f4f6';this.style.borderColor='#d1d5db'"
               onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:#f9fafb;border:1px solid #e5e7eb;color:#d1d5db;cursor:not-allowed;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </div>
</nav>
@endif
