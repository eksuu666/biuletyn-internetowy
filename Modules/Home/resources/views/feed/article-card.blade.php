@php
    $label = \Modules\Category\App\Models\Category::EDITORIAL_FORMATS[$story->category->slug][1] ?? $story->category->name;
    $label = in_array($story->category->slug, ['reportaze', 'reportaz']) ? 'Reportaż' : $label;
    $labelStyle = in_array($story->category->slug, ['reportaze', 'reportaz', 'felietony']) ? 'coral' : (in_array($story->category->slug, ['poradniki', 'adopcje']) ? 'yellow' : 'lavender');
@endphp
<article @class(['home-feed-card', 'home-feed-card-large' => $large, 'home-feed-paid' => $story->is_sponsored]) data-stream-id="article-{{ $story->id }}">
    <div class="home-feed-media">
        <a href="{{ $story->getUrl() }}" tabindex="-1" aria-hidden="true"><img src="{{ $story->image?->url() ?? asset('brand/zwierzaki.webp') }}" alt="" width="{{ $large ? 1240 : 600 }}" height="{{ $large ? 564 : 338 }}" style="object-position: {{ (int) $story->image_focus_x }}% {{ (int) $story->image_focus_y }}%" loading="{{ ($priority ?? false) ? 'eager' : 'lazy' }}" @if($priority ?? false) fetchpriority="high" @endif decoding="async"></a>
        <span class="home-feature-views" aria-label="Liczba odsłon: {{ $story->page_view_count }}"><i class="fa fa-eye" aria-hidden="true"></i> {{ number_format($story->page_view_count, 0, ',', ' ') }}</span>
    </div>
    <div class="home-feed-copy">
        @if($story->is_sponsored)<span class="home-paid-label">Materiał sponsorowany</span>@endif
        <a class="home-feed-label home-label-{{ $labelStyle }}" href="{{ route('categories.show', $story->category->slug) }}">{{ $label }}</a>
        <h2 class="home-feed-title"><a href="{{ $story->getUrl() }}">{{ $story->title }}</a></h2>
        <div class="home-feed-meta">@if($story->is_sponsored && $story->sponsor_name)<span>{{ $story->sponsor_name }}</span>@endif<time datetime="{{ $story->published_at->toAtomString() }}">{{ $story->published_at->locale('pl')->translatedFormat('j M, H:i') }}</time></div>
    </div>
</article>
