@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>{{ $about->title }}</h1>
        <div class="portal-service-grid">
            <div class="portal-prose service-card portal-service-copy">{!! $about->content !!}</div>
            <aside class="service-card portal-service-copy">
                <span class="portal-service-icon" aria-hidden="true"><i class="fa fa-envelope-o"></i></span>
                <h2>Kontakt z redakcją</h2>
                <a class="portal-button" href="{{ route('contact-us.index') }}">Napisz do nas</a>
            </aside>
        </div>
    </div>
@endsection
