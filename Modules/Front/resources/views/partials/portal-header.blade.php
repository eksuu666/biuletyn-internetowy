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
            <a class="portal-brand" href="{{ route('home.index') }}" aria-label="sŁodziaki, strona główna">
                <x-public-wordmark illustrated/>
            </a>
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
                @php
                    $activeTopic = trim($__env->yieldContent('portal-topic'));
                    $latestActive = request()->routeIs('home.*');
                    $aboutActive = request()->routeIs('about-us.index');
                @endphp
                <a href="{{ route('home.latest') }}" @class(['is-current' => $latestActive]) @if($latestActive) aria-current="{{ request()->routeIs('home.latest') ? 'page' : 'true' }}" @endif>Najnowsze</a>
                @foreach(\Modules\Front\App\View\Composers\SharedDataComposer::PORTAL_TOPICS as $slug => $topic)
                    @php
                        $categoryPage = request()->routeIs('categories.show') && request()->route('category')->slug === $slug;
                        $topicSearch = ! $portal_topics->has($slug) && request()->routeIs('search.index') && request('query') === $topic;
                        $active = $categoryPage || $topicSearch || $activeTopic === $slug;
                    @endphp
                    <a href="{{ $portal_topics->has($slug) ? route('categories.show', $slug) : route('search.index', ['query' => $topic]) }}"
                        @class(['is-current' => $active]) @if($active) aria-current="{{ $categoryPage || $topicSearch ? 'page' : 'true' }}" @endif>{{ $topic }}</a>
                @endforeach
                <a href="{{ route('about-us.index') }}" @class(['is-current' => $aboutActive]) @if($aboutActive) aria-current="page" @endif>O redakcji</a>
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
                    @elseif(! $menu->category || ! $portal_topics->has($menu->category->slug))
                        <a href="{{ $menu->getUrl() }}">{{ $menu->getName() }}</a>
                    @endif
                @endforeach
            </nav>
            @if($bulletin_page)
                <a href="{{ $bulletin_page->url() }}" @class(['portal-nav-bulletin', 'is-current' => request()->routeIs('pages.show') && request()->route('page')->is($bulletin_page)]) @if(request()->routeIs('pages.show') && request()->route('page')->is($bulletin_page)) aria-current="page" @endif>Biuletyn <span aria-hidden="true">→</span></a>
            @endif
        </div>
    </div>
</header>
