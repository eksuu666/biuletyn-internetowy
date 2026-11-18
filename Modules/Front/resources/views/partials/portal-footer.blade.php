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
                <a href="{{ route('categories.index') }}" @if(request()->routeIs('categories.index')) aria-current="page" @endif>Wszystkie działy</a>
                <a href="{{ route('about-us.index') }}">O redakcji</a>
                <a href="{{ url('/contact-us') }}">Kontakt</a>
                <a href="{{ route('bulletins.index') }}">Z prasy do sieci!</a>
                <a href="{{ url('/feed') }}">RSS</a>
                <a href="{{ route('newsletters.index') }}">Newsletter</a>
                @guest
                    @if(config('mail.default') !== 'log')
                        <a href="{{ route('register') }}">Załóż konto</a>
                    @endif
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
