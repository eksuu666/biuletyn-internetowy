@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service forum">
        <a class="portal-back" href="{{ route('forum.index') }}">← Forum</a>
        <header class="forum-section-heading service-card">
            <img src="{{ asset('home/forum/'.$section.'.webp') }}" width="640" height="360" alt="">
            <div>
                <h1>{{ $details['name'] }}</h1>
                <p>{{ $details['intro'] }}</p>
                <a class="portal-button" href="{{ route('forum.topics.create', $section) }}">Załóż temat</a>
            </div>
        </header>
        @if($topics->isNotEmpty())
            @include('front::forum.topic-list')
            <x-front-listing-page-pagination :paginator="$topics"/>
        @else
            <p class="service-card portal-service-copy">W tym dziale nie ma jeszcze tematów. Możesz założyć pierwszy.</p>
        @endif
    </div>
@endsection
