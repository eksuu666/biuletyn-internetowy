<header class="portal-header">
    <div class="portal-utility">
        <div class="portal-wrap portal-utility-row">
            <span>Portal o zwierzętach i ich ludziach</span>
        </div>
    </div>
    <div class="portal-masthead">
        <div class="portal-wrap portal-masthead-row">
            <a class="portal-brand" href="{{ route('home.index') }}" aria-label="sŁodziaki, strona główna">
                <x-public-wordmark illustrated/>
            </a>
            <form class="portal-search" action="{{ route('search.index') }}" role="search">
                <label class="sr-only" for="portal-search">Szukaj artykułów</label>
                <input id="portal-search" name="query" type="search" maxlength="200" placeholder="Szukaj artykułów" value="{{ $searchText ?? '' }}">
                <button aria-label="Szukaj"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
            @auth
                <a class="portal-account" href="{{ route(config('app.panel_prefix', 'panel') . '.index') }}"><i class="fa fa-user-o" aria-hidden="true"></i> <span>Moje konto</span></a>
            @else
                <a class="portal-account" href="{{ route('login') }}"><i class="fa fa-user-o" aria-hidden="true"></i> <span>Zaloguj się</span></a>
            @endauth
        </div>
    </div>
    @php
        $portalActive = request()->routeIs('home.*', 'articles.show', 'news.show', 'categories.*', 'tags.*', 'search.index', 'author.index');
        $aboutActive = request()->routeIs('about-us.index');
        $forumActive = request()->routeIs('forum.index');
    @endphp
    <div class="portal-nav">
        <div class="portal-wrap portal-nav-row">
            <nav aria-label="Główna nawigacja">
                <a href="{{ route('home.index') }}" @class(['is-current' => $portalActive]) @if($portalActive) aria-current="{{ request()->routeIs('home.index') ? 'page' : 'true' }}" @endif>Portal</a>
                <a href="{{ route('about-us.index') }}" @class(['is-current' => $aboutActive]) @if($aboutActive) aria-current="page" @endif>O redakcji</a>
                <a href="{{ route('forum.index') }}" @class(['is-current' => $forumActive]) @if($forumActive) aria-current="page" @endif>Forum</a>
            </nav>
            <details class="portal-nav-menu" data-portal-menu>
                <summary aria-controls="portal-site-menu"><i class="fa fa-bars" aria-hidden="true"></i> Menu</summary>
                @include('front::partials.portal-site-menu')
            </details>
        </div>
    </div>
</header>
