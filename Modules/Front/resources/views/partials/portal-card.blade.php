<article class="portal-card">
    <a class="portal-card-media" href="{{ $story->getUrl() }}" tabindex="-1" aria-hidden="true">
        <img src="{{ $story->image ? asset('storage/' . $story->image->file_path) : asset('brand/zwierzaki.webp') }}" alt="" loading="lazy" decoding="async">
    </a>
    <div class="portal-card-content">
        <a class="portal-kicker" href="{{ route('categories.show', $story->category->slug) }}">{{ $story->category->name }}</a>
        <h3><a href="{{ $story->getUrl() }}">{{ $story->title }}</a></h3>
        <p>{{ $story->bodyText(120) }}</p>
        <div class="portal-meta">
            @if($story->user)
                <a href="{{ route('author.index', $story->user->username) }}">{{ $story->user->full_name }}</a>
            @endif
            <time datetime="{{ $story->published_at->toAtomString() }}">{{ $story->published_at->locale('pl')->translatedFormat('j M Y') }}</time>
        </div>
    </div>
</article>
