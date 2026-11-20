@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        @include('front::partials.page-heading', ['title' => $contact->title, 'eyebrow' => 'Porozmawiajmy', 'icon' => 'envelope-o', 'description' => 'Temat do gazety, pytanie albo pomysł na współpracę? Jesteśmy tutaj.', 'headingImage' => 'brand/konto-rysunek.webp'])
        <nav class="portal-section-nav" aria-label="Kontakt z redakcją"><a href="{{ route('contact-us.index') }}" aria-current="page">Napisz do nas</a><a href="{{ route('about-us.index') }}">Poznaj redakcję</a><a href="{{ route('forum.index') }}">Forum sŁodziaków</a></nav>
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
