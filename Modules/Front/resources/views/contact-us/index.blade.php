@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>{{ $contact->title }}</h1>
        <div class="portal-service-grid">
            <section class="service-card portal-service-copy" aria-labelledby="contact-heading">
                @include('front::contact-us.partials.contact-form')
            </section>
            <aside class="service-card portal-service-copy">
                <div class="portal-prose">{!! $contact->content !!}</div>
                @include('front::contact-us.partials.contact-info')
            </aside>
        </div>
    </div>
@endsection
