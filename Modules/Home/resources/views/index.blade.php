@extends('front::layouts.master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('home/css/bulletins.css') }}?v={{ filemtime(public_path('home/css/bulletins.css')) }}">
    <link rel="stylesheet" href="{{ asset('home/css/slodziaki-home.css') }}?v={{ filemtime(public_path('home/css/slodziaki-home.css')) }}">
@endpush

@section('content')
    <div class="portal-wrap portal-home">
        @include('front::partials.portal-ads', ['placements' => $ads['header']])
        <div class="home-feed-layout">
            <section class="home-feed-main" aria-labelledby="home-feed-heading">
                <header class="home-feed-heading">
                    <h1 class="home-heading" id="home-feed-heading"><span class="home-section-icon" aria-hidden="true"><i class="fa fa-newspaper-o"></i></span>W portalu</h1>
                    <a href="{{ route('home.latest') }}">Wszystkie artykuły <span aria-hidden="true">↗</span></a>
                </header>
                @if($feed['items'] !== [])
                    <div class="home-news-grid" data-home-feed>
                        @include('home::feed.items', $feed)
                    </div>
                    <div class="home-feed-sentinel" data-feed-sentinel data-next-url="{{ $feed['nextUrl'] }}">
                        <p data-feed-status role="status" aria-live="polite"></p>
                        <button class="portal-outline" type="button" data-feed-retry hidden>Spróbuj ponownie</button>
                    </div>
                    <noscript><p><a href="{{ route('home.latest') }}">Przeglądaj wszystkie artykuły →</a></p></noscript>
                @else
                    <p class="portal-empty">Pierwsze artykuły pojawią się tutaj po publikacji.</p>
                @endif
                @include('front::partials.portal-ads', ['placements' => $ads['first_section']->concat($ads['second_section'])->concat($ads['third_section'])->concat($ads['fourth_section'])])
            </section>
            <aside class="home-rail" aria-label="Warto zajrzeć">
                <section aria-labelledby="home-print">
                    <h2 class="home-heading" id="home-print"><span class="home-section-icon" aria-hidden="true"><i class="fa fa-newspaper-o"></i></span>Z prasy do sieci!</h2>
                    @include('home::bulletins.shelf', ['issues' => $bulletinIssues])
                </section>
                <div class="home-rail-follow">
                    @if($popular->isNotEmpty())
                        <section class="home-rail-popular" aria-labelledby="home-popular">
                            <h2 id="home-popular">Najczęściej czytane <span>TOP 10</span></h2>
                            <ol>@foreach($popular as $story)<li><a href="{{ $story->getUrl() }}">{{ $story->title }}</a></li>@endforeach</ol>
                            <a class="home-rail-more" href="{{ route('home.popular') }}">Zobacz Top 10 <span aria-hidden="true">→</span></a>
                        </section>
                    @endif
                    <a class="home-rail-forum" href="{{ route('forum.index') }}"><i class="fa fa-comments-o" aria-hidden="true"></i><span><strong>Porozmawiajmy o zwierzakach</strong><small>Dołącz do forum sŁodziaków <span aria-hidden="true">→</span></small></span></a>
                    <a class="home-rail-feature home-rail-weather" href="{{ route('weather.index') }}">
                        <img src="{{ asset('home/illustrations/pogoda.webp') }}" alt="" width="564" height="282" loading="lazy">
                        <span class="home-rail-feature-copy"><small>Na cztery łapy</small><strong>Pogoda na spacer</strong><span>Sprawdź prognozę i mapę <i class="fa fa-angle-right" aria-hidden="true"></i></span></span>
                    </a>
                    <a class="home-rail-feature home-rail-contact" href="{{ route('contact-us.index') }}">
                        <img src="{{ asset('brand/konto-rysunek.webp') }}" alt="" width="564" height="282" loading="lazy">
                        <span class="home-rail-feature-copy"><small>Od Was do redakcji</small><strong>Masz nosa do historii?</strong><span>Napisz do sŁodziaków <i class="fa fa-angle-right" aria-hidden="true"></i></span></span>
                    </a>
                    @include('front::partials.portal-ads', ['placements' => $ads['first_sidebar']->concat($ads['second_sidebar'])])
                    <nav class="home-rail-links" aria-label="Przydatne linki">
                        <a href="{{ route('about-us.index') }}">O redakcji</a><a href="{{ url('/feed') }}">RSS</a>
                    </nav>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('home/js/home-feed.js') }}?v={{ filemtime(public_path('home/js/home-feed.js')) }}" defer></script>
@endpush
