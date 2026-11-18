<article class="portal-card">
    <a class="portal-card-media" href="{{ $story->getUrl() }}" tabindex="-1" aria-hidden="true">
        <img src="{{ $story->image ? asset('storage/' . $story->image->file_path) : asset('brand/zwierzaki.webp') }}" alt="" style="object-position: {{ (int) $story->image_focus_x }}% {{ (int) $story->image_focus_y }}%" loading="lazy" decoding="async">
    </a>
    <div class="portal-card-content">
        @if($story->is_sponsored)<p class="portal-sponsored-disclosure">Materiał sponsorowany{{ $story->sponsor_name ? ' · '.$story->sponsor_name : '' }}</p>@endif
        <a class="portal-kicker" href="{{ route('categories.show', $story->category->slug) }}">{{ $story->category->name }}</a>
        <h3><a href="{{ $story->getUrl() }}">{{ $story->title }}</a></h3>
        @empty($compact)
            <p>{{ $story->bodyText(120) }}</p>
            <div class="portal-meta">
                @if($story->user)
                    <a href="{{ route('author.index', $story->user->username) }}">{{ $story->user->full_name }}</a>
                @endif
                <time datetime="{{ $story->published_at->toAtomString() }}">{{ $story->published_at->locale('pl')->translatedFormat('j M Y') }}</time>
            </div>
        @endempty
    </div>
</article>
