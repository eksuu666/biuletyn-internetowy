@php
    $portalActive = request()->routeIs('home.*', 'articles.show', 'news.show', 'categories.*', 'tags.*', 'search.index', 'author.index');
    $aboutActive = request()->routeIs('about-us.index');
    $forumActive = request()->routeIs('forum.*');
@endphp

<header class="portal-header">
    <div class="portal-wrap portal-header-row">
        <a class="portal-brand" href="{{ route('home.index') }}" aria-label="sŁodziaki, strona główna">
            <x-public-wordmark illustrated/>
        </a>

        <nav class="portal-primary-nav" aria-label="Główna nawigacja">
            <a href="{{ route('home.index') }}" @class(['is-current' => $portalActive]) @if($portalActive) aria-current="{{ request()->routeIs('home.index') ? 'page' : 'true' }}" @endif>Portal</a>
            <a href="{{ route('about-us.index') }}" @class(['is-current' => $aboutActive]) @if($aboutActive) aria-current="page" @endif>O redakcji</a>
            <a href="{{ route('forum.index') }}" @class(['is-current' => $forumActive]) @if($forumActive) aria-current="{{ request()->routeIs('forum.index') ? 'page' : 'true' }}" @endif>Forum</a>
        </nav>

        <form class="portal-search" action="{{ route('search.index') }}" role="search">
            <label class="sr-only" for="portal-search">Szukaj artykułów</label>
            <input id="portal-search" name="query" type="search" maxlength="200" placeholder="Szukaj artykułów" value="{{ $searchText ?? '' }}">
            <button aria-label="Szukaj"><i class="fa fa-search" aria-hidden="true"></i></button>
        </form>

        @auth
            <a class="portal-account" href="{{ route(config('app.panel_prefix', 'panel') . '.index') }}"><i class="fa fa-user-o" aria-hidden="true"></i><span>Moje konto</span></a>
        @else
            <a class="portal-account" href="{{ route('login') }}"><i class="fa fa-user-o" aria-hidden="true"></i><span>Zaloguj się</span></a>
        @endauth

        <details class="portal-nav-menu" data-portal-menu>
            <summary aria-controls="portal-site-menu"><i class="fa fa-bars portal-menu-open" aria-hidden="true"></i><i class="fa fa-times portal-menu-close" aria-hidden="true"></i> Menu</summary>
            @include('front::partials.portal-site-menu')
        </details>
    </div>
</header>
