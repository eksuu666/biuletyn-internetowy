@extends('front::layouts.master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('home/css/slodziaki-home.css') }}?v={{ filemtime(public_path('home/css/slodziaki-home.css')) }}">
@endpush

@section('content')
    @php
        $adoptions = $homeCategories->get('adopcje');
        $adoptionsUrl = $adoptions ? route('categories.show', $adoptions->slug) : route('search.index', ['query' => 'Adopcje']);
        $formatLabel = \Modules\Category\App\Models\Category::EDITORIAL_FORMATS[$featured?->category->slug][1] ?? null;
        $featureLabel = match ($featured?->category->slug) {
            'reportaze', 'reportaz' => 'Reportaż',
            'aktualnosci' => __('article::types.' . $featured->type),
            default => $formatLabel ?? $featured?->category->name ?? 'Od redakcji',
        };
    @endphp
    <div class="portal-wrap portal-home">
        @include('front::partials.portal-ads', ['placements' => $ads['header']])
        <div class="home-columns">
            <section aria-labelledby="home-featured">
                <h1 class="home-heading" id="home-featured">Polecane</h1>
                <article class="home-feature">
                    <a href="{{ $featured ? $featured->getUrl() : route('about-us.index') }}" tabindex="-1" aria-hidden="true">
                        <img src="{{ $featured?->image ? asset('storage/' . $featured->image->file_path) : asset('brand/zwierzaki.webp') }}" alt="" width="578" height="296" fetchpriority="high">
                    </a>
                    @if($featured)
                        <a class="home-label home-label-feature" href="{{ route('categories.show', $featured->category->slug) }}">{{ $featureLabel }}</a>
                    @else
                        <span class="home-label home-label-feature">{{ $featureLabel }}</span>
                    @endif
                    <h2><a href="{{ $featured ? $featured->getUrl() : route('about-us.index') }}">{{ $featured?->title ?? 'Życie ze zwierzętami' }}</a></h2>
                    <p>{{ $featured?->bodyText(160) ?? 'Porady dla opiekunów, historie adopcji i wiadomości ze świata zwierząt.' }}</p>
                </article>
            </section>

            <section aria-labelledby="home-latest">
                <h2 class="home-heading" id="home-latest">Najnowsze</h2>
                <div class="home-latest">
                    @if($latest->isNotEmpty())
                        <ul>
                            @foreach($latest as $story)
                                <li>
                                    <a href="{{ $story->getUrl() }}">
                                        @if($loop->index < 3 && $story->image)
                                            <img src="{{ asset('storage/' . $story->image->file_path) }}" alt="" width="74" height="67" loading="lazy">
                                        @endif
                                        <span><small>{{ $story->category->name }}</small>{{ $story->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>Pierwsze wiadomości pojawią się tutaj po publikacji.</p>
                    @endif
                    <a class="home-section-link" href="{{ route('home.latest') }}">Wszystkie artykuły <span aria-hidden="true">→</span></a>
                </div>
            </section>

            <section aria-labelledby="home-adoptions">
                <h2 class="home-heading" id="home-adoptions">Adopcje</h2>
                <div class="home-side">
                    <div class="home-adoption">
                        @if($adoptions?->image)
                            <img src="{{ asset('storage/' . $adoptions->image->file_path) }}" alt="{{ $adoptions->image->alt_text }}" width="258" height="155" loading="lazy">
                        @endif
                        <h3>Szukają domu</h3>
                        <p>Psy i koty do adopcji</p>
                        <a class="home-adoption-link" href="{{ $adoptionsUrl }}">{{ $adoptions ? 'Zobacz ogłoszenia' : 'Szukaj materiałów o adopcji' }}</a>
                    </div>
                    @if($bulletin)
                        <div class="home-bulletin">
                            @if($bulletin->image)
                                <img src="{{ asset('storage/' . $bulletin->image->file_path) }}" alt="{{ $bulletin->image->alt_text }}" width="74" height="109" loading="lazy">
                            @endif
                            <div>
                                <p class="home-bubble">Z papieru do sieci!</p>
                                <h3>{{ $bulletin->title }}</h3>
                                <a class="home-section-link" href="{{ $bulletin->url() }}">Czytaj biuletyn <span aria-hidden="true">→</span></a>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        @include('front::partials.portal-ads', ['placements' => $ads['first_section']])
        @if($guides->isNotEmpty())
            <section class="home-guides" aria-labelledby="home-guides-heading">
                <div class="home-guides-header">
                    <h2 class="home-heading" id="home-guides-heading">Poradniki</h2>
                    <a class="home-section-link" href="{{ route('categories.show', 'poradniki') }}">Wszystkie poradniki <span aria-hidden="true">→</span></a>
                </div>
                <div class="portal-cards">
                    @foreach($guides as $story)
                        @include('front::partials.portal-card', ['compact' => true])
                    @endforeach
                </div>
            </section>
        @endif
        @include('front::partials.portal-ads', ['placements' => $ads['second_section']->concat($ads['third_section'])->concat($ads['fourth_section'])->concat($ads['first_sidebar'])->concat($ads['second_sidebar'])])
    </div>
@endsection
