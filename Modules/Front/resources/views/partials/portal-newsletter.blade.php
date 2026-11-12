<section class="portal-newsletter" id="newsletter" aria-labelledby="newsletter-heading">
    <div>
        <span class="portal-kicker">Newsletter</span>
        <h2 id="newsletter-heading">Łódź w Twojej skrzynce.</h2>
        @if(config('mail.default') !== 'log')
            <p>Najważniejsze teksty sŁodziaka prosto na Twój e-mail.</p>
        @else
            <p>Zapisy są obecnie zamknięte.</p>
        @endif
    </div>
    @if(config('mail.default') !== 'log')
        <form action="{{ route('newsletters.subscribe') }}" method="post">
            @csrf
            @honeypot
            <div class="portal-newsletter-field">
                <label class="sr-only" for="newsletter-email">Adres e-mail</label>
                <input id="newsletter-email" type="email" name="email" placeholder="Twój adres e-mail" required autocomplete="email">
                <button class="portal-button">Zapisz się</button>
            </div>
            <label class="portal-consent"><input type="checkbox" name="consent" value="1" required> Zgadzam się na newsletter. Potwierdzę zapis e-mailem i mogę się wypisać w każdej chwili.</label>
        </form>
    @else
        <a class="portal-button" href="{{ url('/feed') }}">Obserwuj przez RSS <span aria-hidden="true">↗</span></a>
    @endif
</section>
