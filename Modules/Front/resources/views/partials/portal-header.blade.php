<header class="portal-header">
    <div class="portal-utility">
        <div class="portal-wrap portal-utility-row">
            <span>Portal o zwierzętach i ich ludziach</span>
            <a href="{{ url('/contact-us') }}">Kontakt</a>
            <a href="{{ url('/feed') }}">RSS</a>
        </div>
    </div>
    <div class="portal-masthead">
        <div class="portal-wrap portal-masthead-row">
            <a class="portal-brand" href="{{ route('home.index') }}" aria-label="sŁodziaki, strona główna"><x-brand-logo :site="$site_details ?? null"/></a>
            <form class="portal-search" action="{{ route('search.index') }}" role="search">
                <label class="sr-only" for="portal-search">Szukaj artykułów</label>
                <input id="portal-search" name="query" type="search" maxlength="200" placeholder="Szukaj artykułów" value="{{ request()->routeIs('search.index') ? request('query', request('text')) : '' }}">
                <button aria-label="Szukaj"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
            @auth
                <a class="portal-account" href="{{ route(config('app.panel_prefix', 'panel') . '.index') }}"><i class="fa fa-user-o" aria-hidden="true"></i> <span>Moje konto</span></a>
            @else
                <a class="portal-account" href="{{ route('login') }}"><i class="fa fa-user-o" aria-hidden="true"></i> <span>Zaloguj się</span></a>
            @endauth
        </div>
    </div>
    <div class="portal-nav">
        <div class="portal-wrap portal-nav-row">
            <nav aria-label="Główna nawigacja">
                <a href="{{ route('home.latest') }}" @class(['is-current' => request()->routeIs('home.*')])>Najnowsze</a>
                @foreach($main_nav['categories']->take(6) as $navCategory)
                    <a href="{{ route('categories.show', $navCategory->slug) }}" @class(['is-current' => request()->routeIs('categories.show') && request()->route('category')->is($navCategory)])>{{ $navCategory->name }}</a>
                @endforeach
                @foreach(['Adopcje', 'Poradniki', 'Reportaże'] as $topic)
                    @unless($main_nav['categories']->take(6)->contains('slug', Str::slug($topic)))
                        <a href="{{ route('search.index', ['query' => $topic]) }}">{{ $topic }}</a>
                    @endunless
                @endforeach
                <a href="{{ route('about-us.index') }}" @class(['is-current' => request()->routeIs('about-us.index')])>O redakcji</a>
                @foreach($main_nav['menus'] as $menu)
                    @if($menu->isMainMenuWithChildren())
                        <details class="portal-nav-menu">
                            <summary>{{ $menu->getName() }}</summary>
                            <div>
                                <a href="{{ $menu->getUrl() }}">{{ $menu->getName() }}</a>
                                @foreach($menu->children->filter('status') as $childMenu)
                                    <a href="{{ $childMenu->getUrl() }}">{{ $childMenu->getName() }}</a>
                                @endforeach
                            </div>
                        </details>
                    @elseif(! $menu->category || ! $main_nav['categories']->contains('id', $menu->category->id))
                        <a href="{{ $menu->getUrl() }}">{{ $menu->getName() }}</a>
                    @endif
                @endforeach
                <a href="{{ route('categories.index') }}" @class(['is-current' => request()->routeIs('categories.index')])>Wszystkie działy</a>
                @if($bulletin_page)
                    <a href="{{ $bulletin_page->url() }}">Biuletyn</a>
                @endif
            </nav>
        </div>
    </div>
</header>
