<nav class="portal-section-nav" aria-label="{{ ($forumNav ?? false) ? 'Nawigacja forum' : 'Nawigacja portalu' }}">
    @if($forumNav ?? false)
        <a href="{{ route('forum.index') }}" @if(request()->routeIs('forum.index')) aria-current="page" @endif>Wszystkie działy</a>
        @foreach(\Modules\Front\App\Models\ForumTopic::SECTIONS as $navSlug => $navSection)
            <a href="{{ route('forum.sections.show', $navSlug) }}" @if(($section ?? $topic->section ?? null) === $navSlug) aria-current="page" @endif>{{ $navSection['name'] }}</a>
        @endforeach
    @else
        <a href="{{ route('home.latest') }}" @if(request()->routeIs('home.latest')) aria-current="page" @endif>Najnowsze</a>
        <a href="{{ route('home.popular') }}" @if(request()->routeIs('home.popular')) aria-current="page" @endif>Top 10</a>
        <a href="{{ route('categories.index') }}" @if(request()->routeIs('categories.*')) aria-current="page" @endif>Działy i tematy</a>
        <a href="{{ route('bulletins.index') }}" @if(request()->routeIs('bulletins.index')) aria-current="page" @endif>Gazety PDF</a>
        <a href="{{ route('saved.index') }}" @if(request()->routeIs('saved.index')) aria-current="page" @endif><i class="fa fa-bookmark-o" aria-hidden="true"></i> Zapisane</a>
    @endif
</nav>
