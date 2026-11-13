@props(['illustrated' => false])

<span class="public-brand">
    <span class="public-wordmark">sŁodziaki</span>
    @if($illustrated)
        <span class="public-spark" aria-hidden="true">✦</span>
        <svg class="public-cat" width="80" height="48" viewBox="0 0 80 48" fill="none" aria-hidden="true">
            <ellipse cx="33" cy="26" rx="33" ry="22" fill="#F0EBFA"/>
            <path d="M12 43 10 33 14 8l11 11 14-1L51 8l4 24-1 7-5 4z" fill="#FAF9F6" stroke="#252336" stroke-width="2"/>
            <path d="m0 27 13 3M0 34l13-2m42-2 10-4m-10 6 10 3M30 32l3 3 4-3" stroke="#252336" stroke-width="1.5"/>
            <circle cx="22" cy="29" r="1.5" fill="#252336"/>
            <circle cx="43" cy="28" r="1.5" fill="#252336"/>
            <path d="m72 16 6-8" stroke="#A83745" stroke-width="1.5"/>
        </svg>
    @endif
</span>
