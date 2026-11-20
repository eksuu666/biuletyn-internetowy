@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        @include('front::partials.page-heading', ['title' => 'Newsletter', 'eyebrow' => 'Bądźmy w kontakcie', 'icon' => 'paper-plane-o', 'description' => 'Nowe historie sŁodziaków prosto do Twojej skrzynki.', 'headingImage' => 'brand/konto-rysunek.webp'])
        @if(session('newsletter_status'))
            <p class="discussion-status" role="status">{{ session('newsletter_status') }}</p>
        @endif
        <x-common-error-messages/>
        @include('front::partials.portal-newsletter')
    </div>
@endsection
