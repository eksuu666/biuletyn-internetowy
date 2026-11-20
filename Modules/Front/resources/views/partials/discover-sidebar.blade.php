<aside class="portal-discover" aria-label="W sŁodziakach">
    <section class="portal-discover-account">
        <span class="portal-page-eyebrow"><i class="fa fa-comments-o" aria-hidden="true"></i>Społeczność</span>
        <h2>{{ request()->routeIs('forum.*') ? 'Dołącz do rozmowy' : 'Poznaj innych zwierzolubów' }}</h2>
        @auth
            <p>Miło Cię widzieć, {{ auth()->user()->full_name }}.</p>
            <a class="portal-button" href="{{ route('forum.index') }}">Przeglądaj forum</a>
        @else
            <p>Twoje doświadczenie może pomóc innym.</p>
            <a class="portal-button" href="{{ route('login') }}">Zaloguj się</a>
        @endauth
    </section>
    <a class="portal-discover-card" href="{{ route('weather.index') }}">
        <img src="{{ asset('home/illustrations/pogoda.webp') }}" alt="" width="564" height="376" loading="lazy">
        <span><small>Na cztery łapy</small><strong>Pogoda na spacer</strong><span>Prognoza i mapa <i class="fa fa-angle-right" aria-hidden="true"></i></span></span>
    </a>
    @unless(request()->routeIs('contact-us.index'))
        <a class="portal-discover-contact" href="{{ route('contact-us.index') }}"><i class="fa fa-envelope-o" aria-hidden="true"></i><span><strong>Masz temat dla redakcji?</strong><small>Napisz do sŁodziaków</small></span><i class="fa fa-angle-right" aria-hidden="true"></i></a>
    @endunless
</aside>
