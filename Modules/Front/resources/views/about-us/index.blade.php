@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        @include('front::partials.page-heading', ['title' => $about->title, 'eyebrow' => 'Poznaj sŁodziaki', 'icon' => 'paw', 'description' => 'Łączy nas ciekawość świata zwierząt i historie ich ludzi.', 'headingImage' => 'brand/konto-rysunek.webp'])
        <div class="portal-page-content portal-text-page">
            <div class="portal-prose service-card portal-service-copy">{!! $about->content !!}</div>
        </div>
    </div>
@endsection
