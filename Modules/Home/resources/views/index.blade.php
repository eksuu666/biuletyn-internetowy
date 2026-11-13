@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-home">
        @include('front::partials.portal-ads', ['placements' => $ads['header']])
        <div class="portal-top-grid">
            <section aria-labelledby="portal-featured">
                <h1 class="portal-column-title" id="portal-featured">Polecane</h1>
                @if($featured)
                    <article class="portal-hero">
                        <img src="{{ $featured->image ? asset('storage/' . $featured->image->file_path) : asset('brand/zwierzaki.webp') }}" alt="{{ $featured->image?->alt_text ?? '' }}" fetchpriority="high">
                        <div class="portal-hero-copy">
                            <a class="portal-hero-tag" href="{{ route('categories.show', $featured->category->slug) }}">{{ $featured->category->name }}</a>
                            <h2><a href="{{ $featured->getUrl() }}">{{ $featured->title }}</a></h2>
                            <p>{{ $featured->bodyText(160) }}</p>
                            <div class="portal-meta">
                                @if($featured->user)
                                    <a href="{{ route('author.index', $featured->user->username) }}">{{ $featured->user->full_name }}</a>
                                @endif
                                <time datetime="{{ $featured->published_at->toAtomString() }}">{{ $featured->published_at->locale('pl')->translatedFormat('j M Y') }}</time>
                            </div>
                        </div>
                    </article>
                @else
                    <section class="portal-hero portal-hero-intro">
                        <img src="{{ asset('brand/zwierzaki.webp') }}" alt="Pies i kot na ławce w ogrodzie" fetchpriority="high">
                        <div class="portal-hero-copy">
                            <h2>Życie ze zwierzętami</h2>
                            <p>Porady dla opiekunów, historie adopcji i wiadomości ze świata zwierząt.</p>
                            <a class="portal-button" href="{{ route('about-us.index') }}">Poznaj redakcję <span aria-hidden="true">↗</span></a>
                        </div>
                    </section>
                @endif
            </section>

            <aside class="portal-brief" aria-labelledby="portal-brief-heading">
                <h2 class="portal-column-title" id="portal-brief-heading">Najnowsze</h2>
                @if($latest->isNotEmpty())
                    <ol>
                        @foreach($latest as $story)
                            <li>
                                <a href="{{ $story->getUrl() }}" class="portal-latest-story">
                                    <img src="{{ $story->image ? asset('storage/' . $story->image->file_path) : asset('brand/zwierzaki.webp') }}" alt="" width="72" height="64" loading="lazy">
                                    <span><small>{{ $story->category->name }}</small>{{ $story->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                    <a class="portal-text-link" href="{{ route('home.latest') }}">Wszystkie artykuły <span aria-hidden="true">→</span></a>
                @else
                    <div class="portal-brief-empty">
                        <span class="portal-kicker">Od redakcji</span>
                        <h3>Pracujemy nad pierwszym wydaniem.</h3>
                        <p>Znasz historię zwierzaka, którą warto opowiedzieć? Daj nam znać.</p>
                        <a class="portal-text-link" href="{{ url('/contact-us') }}">Napisz do nas <span aria-hidden="true">→</span></a>
                    </div>
                @endif
            </aside>

            <aside class="portal-home-aside" aria-labelledby="portal-adoptions">
                <h2 class="portal-column-title" id="portal-adoptions">Adopcje</h2>
                <div class="portal-home-promo">
                    @if($adoption?->image)
                        <img src="{{ asset('storage/' . $adoption->image->file_path) }}" alt="{{ $adoption->image->alt_text }}" width="320" height="180" loading="lazy">
                    @endif
                    <h3>{{ $adoption?->title ?? 'Szukają domu' }}</h3>
                    @unless($adoption)
                        <p>Psy i koty do adopcji</p>
                    @endunless
                    <a class="portal-button" href="{{ $adoption?->getUrl() ?? route('search.index', ['query' => 'Adopcje']) }}">{{ $adoption ? 'Przeczytaj artykuł' : 'Szukaj materiałów o adopcji' }}</a>
                </div>
                @if($bulletin)
                    <div class="portal-home-promo portal-bulletin">
                        <h3>{{ $bulletin->title }}</h3>
                        <a class="portal-text-link" href="{{ $bulletin->url() }}">Czytaj biuletyn <span aria-hidden="true">→</span></a>
                    </div>
                @endif
            </aside>
        </div>

        @include('front::partials.portal-ads', ['placements' => $ads['first_section']])
        @if($guides->isNotEmpty())
            <section class="portal-section" aria-labelledby="portal-stories">
                <div class="portal-section-heading">
                    <h2 id="portal-stories">Poradniki</h2>
                    <a href="{{ route('categories.show', 'poradniki') }}">Wszystkie poradniki <span aria-hidden="true">↗</span></a>
                </div>
                <div class="portal-cards">
                    @foreach($guides as $story)
                        @include('front::partials.portal-card')
                    @endforeach
                </div>
            </section>
        @endif
        @include('front::partials.portal-ads', ['placements' => $ads['second_section']->concat($ads['third_section'])->concat($ads['fourth_section'])->concat($ads['first_sidebar'])->concat($ads['second_sidebar'])])
        @include('front::partials.portal-newsletter')
    </div>
@endsection
