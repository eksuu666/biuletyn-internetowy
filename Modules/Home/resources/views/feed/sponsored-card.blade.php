<article @class(['home-feed-ad', 'home-feed-ad-large' => $large]) data-stream-id="ad-{{ $ad->id }}">
    <span class="home-paid-label">Reklama{{ $ad->sponsor_name ? ' · '.$ad->sponsor_name : '' }}</span>
    <a class="home-feed-ad-image" href="{{ $ad->link }}" rel="sponsored noopener noreferrer"><img src="{{ $ad->image->url() }}" alt="{{ $ad->image->alt_text ?: $ad->title }}" loading="lazy" decoding="async"></a>
    <h2><a href="{{ $ad->link }}" rel="sponsored noopener noreferrer">{{ $ad->title }}</a></h2>
</article>
