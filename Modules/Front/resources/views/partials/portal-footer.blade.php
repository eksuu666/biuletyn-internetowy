<footer class="portal-footer">
    <div class="portal-wrap">
        <div class="portal-footer-grid">
            <div class="portal-footer-brand">
                <a href="{{ route('home.index') }}" aria-label="sŁodziaki, strona główna"><x-public-wordmark/></a>
                <p>Wiadomości, historie i porady<br>o zwierzętach i ich ludziach.</p>
                <a class="portal-footer-contact" href="{{ route('contact-us.index') }}">Napisz do redakcji <span aria-hidden="true">↗</span></a>
            </div>
            <nav aria-label="Portal - stopka"><h2>Portal</h2><a href="{{ route('home.latest') }}">Najnowsze artykuły</a><a href="{{ route('home.popular') }}">Najczęściej czytane</a><a href="{{ route('categories.index') }}">Działy i tematy</a><a href="{{ route('bulletins.index') }}">Z prasy do sieci!</a></nav>
            <nav aria-label="Społeczność - stopka"><h2>Bądźmy w kontakcie</h2><a href="{{ route('forum.index') }}">Forum sŁodziaków</a><a href="{{ route('about-us.index') }}">O redakcji</a><a href="{{ route('contact-us.index') }}">Kontakt i współpraca</a><a href="{{ route('newsletters.index') }}">Newsletter</a></nav>
            <nav aria-label="Przydatne - stopka"><h2>Pod ręką</h2><a href="{{ route('weather.index') }}">Pogoda</a><a href="{{ route('saved.index') }}">Zapisane artykuły</a><a href="{{ url('/feed') }}">Kanał RSS</a>@guest<a href="{{ route('login') }}">Zaloguj się</a>@endguest @auth<form action="{{ route('logout') }}" method="post">@csrf<button type="submit">Wyloguj się</button></form>@endauth</nav>
        </div>
        <div class="portal-footer-bottom">
            <span>© {{ now()->year }} sŁodziaki</span>
            <div class="portal-footer-social">@foreach($social_networks as $name => $url)<a href="{{ $url }}" rel="noopener noreferrer">{{ ucfirst($name) }}</a>@endforeach</div>
            <a class="portal-footer-top" href="#">Na górę <span aria-hidden="true">↑</span></a>
        </div>
    </div>
</footer>
