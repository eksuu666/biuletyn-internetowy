@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>{{ $contact->title }}</h1>
        <div class="portal-static-content">
            <div class="portal-prose">{!! $contact->content !!}</div>
            @include('front::contact-us.partials.contact-info')
            @include('front::contact-us.partials.contact-form')
        </div>
    </div>
@endsection
