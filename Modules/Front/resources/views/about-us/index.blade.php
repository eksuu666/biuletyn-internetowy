@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        @include('front::partials.page-heading', ['title' => $about->title, 'eyebrow' => 'Poznaj sŁodziaki', 'icon' => 'paw', 'description' => 'Łączy nas ciekawość świata zwierząt i historie ich ludzi.', 'headingImage' => 'brand/konto-rysunek.webp'])
        <nav class="portal-section-nav" aria-label="O redakcji"><a href="{{ route('about-us.index') }}" aria-current="page">O nas</a><a href="{{ route('contact-us.index') }}">Kontakt i współpraca</a><a href="{{ route('bulletins.index') }}">Nasze gazety</a></nav>
        <div class="portal-service-grid">
            <div class="portal-prose service-card portal-service-copy">{!! $about->content !!}</div>
            @include('front::partials.discover-sidebar')
        </div>
    </div>
@endsection
