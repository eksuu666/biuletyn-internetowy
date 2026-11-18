@extends('front::layouts.master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('home/css/bulletins.css') }}?v={{ filemtime(public_path('home/css/bulletins.css')) }}">
    <link rel="stylesheet" href="{{ asset('home/css/slodziaki-home.css') }}?v={{ filemtime(public_path('home/css/slodziaki-home.css')) }}">
@endpush

@section('content')
    @php
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
                <h1 class="home-heading" id="home-featured">
                    <span class="home-section-icon" aria-hidden="true"><i class="fa fa-bookmark-o"></i></span>
                    Polecane
                </h1>
                <article class="home-feature">
                    <div class="home-feature-media">
                        <a href="{{ $featured ? $featured->getUrl() : route('about-us.index') }}" tabindex="-1" aria-hidden="true">
                            <img src="{{ $featured?->image ? asset('storage/' . $featured->image->file_path) : asset('brand/zwierzaki.webp') }}" alt="" width="578" height="296" fetchpriority="high">
                        </a>
                        @if($featured)
                            <span class="home-feature-views" aria-label="Liczba odsłon: {{ $featured->page_view_count }}">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                                {{ number_format($featured->page_view_count, 0, ',', ' ') }}
                            </span>
                        @endif
                    </div>
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
                <h2 class="home-heading" id="home-latest">
                    <span class="home-section-icon" aria-hidden="true"><i class="fa fa-clock-o"></i></span>
                    Najnowsze
                </h2>
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
                    <div class="home-latest-links">
                        <a class="home-section-link" href="{{ route('home.latest') }}">Wszystkie artykuły <span aria-hidden="true">→</span></a>
                        <a class="home-section-link" href="{{ route('home.popular') }}">Najczęściej czytane <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </section>

            <section aria-labelledby="home-print">
                <h2 class="home-heading" id="home-print">
                    <span class="home-section-icon" aria-hidden="true"><i class="fa fa-newspaper-o"></i></span>
                    Z prasy do sieci!
                </h2>
                <div class="home-side">
                    @include('home::bulletins.shelf', ['issues' => $bulletinIssues])
                </div>
            </section>
        </div>

        @include('front::partials.portal-ads', ['placements' => $ads['first_section']])
        @if($guides->isNotEmpty())
            <section class="home-guides" aria-labelledby="home-guides-heading">
                <div class="home-guides-header">
                    <h2 class="home-heading" id="home-guides-heading">
                        <span class="home-section-icon" aria-hidden="true"><i class="fa fa-compass"></i></span>
                        Poradniki
                    </h2>
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
