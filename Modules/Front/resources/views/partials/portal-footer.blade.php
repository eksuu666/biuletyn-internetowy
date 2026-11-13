<footer class="portal-footer">
    <div class="portal-wrap">
        <div class="portal-footer-row">
            <div>
                <a class="portal-brand" href="{{ route('home.index') }}">
                    <x-public-wordmark/>
                </a>
                <p>Portal o zwierzętach i ich ludziach</p>
            </div>
            <nav aria-label="Stopka">
                @unless(request()->routeIs('home.index'))
                    <a href="{{ route('home.latest') }}">Najnowsze</a>
                @endunless
                <a href="{{ route('about-us.index') }}">O redakcji</a>
                <a href="{{ url('/contact-us') }}">Kontakt</a>
                @if($bulletin_page)
                    <a href="{{ $bulletin_page->url() }}">Biuletyn</a>
                @endif
                <a href="{{ url('/feed') }}">RSS</a>
                @guest
                    @unless(request()->routeIs('home.index'))
                        <a href="{{ route('register') }}">Załóż konto</a>
                    @endunless
                @endguest
                @auth
                    <form action="{{ route('logout') }}" method="post">@csrf<button type="submit">Wyloguj się</button></form>
                @endauth
            </nav>
        </div>
        <div class="portal-footer-bottom">
            <span>© {{ now()->year }} sŁodziaki</span>
            @foreach($social_networks as $name => $url)
                <a href="{{ $url }}" rel="noopener noreferrer">{{ ucfirst($name) }}</a>
            @endforeach
        </div>
    </div>
</footer>
