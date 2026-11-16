<nav class="portal-menu-panel" id="portal-site-menu" aria-label="Menu portalu">
    <section aria-labelledby="portal-menu-news">
        <h2 id="portal-menu-news">Portal</h2>
        <a href="{{ route('home.latest') }}" @if(request()->routeIs('home.latest')) aria-current="page" @endif>Najnowsze</a>
        <a href="{{ route('search.index') }}" @if(request()->routeIs('search.index')) aria-current="page" @endif>Szukaj w portalu</a>
        @if($bulletin_page)
            <a href="{{ $bulletin_page->url() }}">Biuletyn</a>
        @endif
        @foreach($main_nav['menus'] as $menu)
            @if(! $menu->category || ! $portal_topics->has($menu->category->slug))
                <a href="{{ $menu->getUrl() }}">{{ $menu->getName() }}</a>
            @endif
            @if($menu->isMainMenuWithChildren())
                @foreach($menu->children->filter('status') as $childMenu)
                    <a href="{{ $childMenu->getUrl() }}">{{ $childMenu->getName() }}</a>
                @endforeach
            @endif
        @endforeach
    </section>

    <section aria-labelledby="portal-menu-topics">
        <h2 id="portal-menu-topics">Działy</h2>
        @foreach(\Modules\Category\App\Models\Category::PORTAL_TOPICS as $slug => $topic)
            <a href="{{ $portal_topics->has($slug) ? route('categories.show', $slug) : route('search.index', ['query' => $topic]) }}"
                @if(request()->routeIs('categories.show') && request()->route('category')->slug === $slug) aria-current="page" @endif>{{ $topic }}</a>
        @endforeach
        <a href="{{ route('categories.index') }}" @if(request()->routeIs('categories.index')) aria-current="page" @endif>Wszystkie działy</a>
    </section>

    <section aria-labelledby="portal-menu-services">
        <h2 id="portal-menu-services">Przydatne</h2>
        <a href="{{ route('weather.index') }}" @if(request()->routeIs('weather.index')) aria-current="page" @endif><i class="fa fa-sun-o" aria-hidden="true"></i> Pogoda</a>
        <a href="{{ route('contact-us.index') }}" @if(request()->routeIs('contact-us.index')) aria-current="page" @endif><i class="fa fa-envelope-o" aria-hidden="true"></i> Kontakt</a>
        <a href="{{ route('newsletters.index') }}" @if(request()->routeIs('newsletters.index')) aria-current="page" @endif><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Newsletter</a>
        <a href="{{ url('/feed') }}"><i class="fa fa-rss" aria-hidden="true"></i> RSS</a>
    </section>
</nav>
