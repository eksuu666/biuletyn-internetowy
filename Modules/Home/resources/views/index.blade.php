@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-home">
        @include('front::partials.portal-ads', ['placements' => $ads['header']])
        <div class="portal-section-heading">
            <h1>Polecane</h1>
            <a href="{{ route('home.latest') }}">Wszystkie wiadomości <span aria-hidden="true">↗</span></a>
        </div>

        <div class="portal-top-grid">
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

            <aside class="portal-brief" aria-labelledby="portal-brief-heading">
                <h2 id="portal-brief-heading"><span aria-hidden="true"></span> Najnowsze</h2>
                @if($latest->isNotEmpty())
                    <ol>
                        @foreach($latest->take(5) as $story)
                            <li>
                                <time datetime="{{ $story->published_at->toAtomString() }}">{{ $story->published_at->isToday() ? $story->published_at->format('H:i') : $story->published_at->locale('pl')->translatedFormat('j M') }}</time>
                                <a href="{{ $story->getUrl() }}">{{ $story->title }}</a>
                            </li>
                        @endforeach
                    </ol>
                    <a class="portal-text-link" href="{{ route('home.latest') }}">Przejdź do najnowszych <span aria-hidden="true">→</span></a>
                @else
                    <div class="portal-brief-empty">
                        <span class="portal-kicker">Od redakcji</span>
                        <h3>Pracujemy nad pierwszym wydaniem.</h3>
                        <p>Znasz historię zwierzaka, którą warto opowiedzieć? Daj nam znać.</p>
                        <a class="portal-text-link" href="{{ url('/contact-us') }}">Napisz do nas <span aria-hidden="true">→</span></a>
                    </div>
                @endif
            </aside>
        </div>

        <nav class="portal-topics" aria-label="Szukaj według tematu">
            <strong>Szukaj według tematu</strong>
            @foreach(['Psy', 'Koty', 'Adopcje', 'Opieka'] as $topic)
                <a href="{{ route('search.index', ['query' => $topic]) }}">{{ $topic }} <span aria-hidden="true">↗</span></a>
            @endforeach
        </nav>

        @include('front::partials.portal-ads', ['placements' => $ads['first_section']])
        @if($stories->isNotEmpty())
            <section class="portal-section" aria-labelledby="portal-stories">
                <div class="portal-section-heading">
                    <h2 id="portal-stories">Wiadomości i tematy</h2>
                    <a href="{{ route('home.latest') }}">Zobacz więcej <span aria-hidden="true">↗</span></a>
                </div>
                <div class="portal-cards">
                    @foreach($stories as $story)
                        @include('front::partials.portal-card')
                    @endforeach
                </div>
            </section>
        @endif
        @include('front::partials.portal-ads', ['placements' => $ads['second_section']->concat($ads['third_section'])->concat($ads['fourth_section'])->concat($ads['first_sidebar'])->concat($ads['second_sidebar'])])
        @include('front::partials.portal-newsletter')
    </div>
@endsection
