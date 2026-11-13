<footer class="portal-footer">
    <div class="portal-wrap">
        <div class="portal-footer-row">
            <div>
                <a class="portal-brand" href="{{ route('home.index') }}"><x-brand-logo :site="$site_details ?? null"/></a>
                <p>{{ $site_details?->footer_text ?: 'Porady, adopcje i historie o zwierzętach.' }}</p>
            </div>
            <nav aria-label="Stopka">
                <a href="{{ route('home.latest') }}">Najnowsze</a>
                <a href="{{ route('about-us.index') }}">O redakcji</a>
                <a href="{{ url('/contact-us') }}">Kontakt</a>
                @if($bulletin_page)
                    <a href="{{ $bulletin_page->url() }}">Biuletyn</a>
                @endif
                <a href="{{ url('/feed') }}">RSS</a>
                @guest <a href="{{ route('register') }}">Załóż konto</a> @endguest
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
