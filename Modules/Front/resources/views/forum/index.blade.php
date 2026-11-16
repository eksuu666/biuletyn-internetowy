@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service">
        <header class="portal-list-header">
            <h1>Forum</h1>
            <p>Dyskusje pod artykułami</p>
        </header>

        @if($articles->isNotEmpty())
            <ul class="portal-forum-list service-card">
                @foreach($articles as $story)
                    <li>
                        <span class="portal-service-icon" aria-hidden="true"><i class="fa fa-comments-o"></i></span>
                        <div>
                            <a class="portal-kicker" href="{{ route('categories.show', $story->category->slug) }}">{{ $story->category->name }}</a>
                            <h2><a href="{{ $story->getUrl() }}#comments">{{ $story->title }} <span aria-hidden="true">→</span></a></h2>
                        </div>
                    </li>
                @endforeach
            </ul>
            <x-front-listing-page-pagination :paginator="$articles"/>
        @else
            <div class="service-card portal-service-empty">
                <span class="portal-service-icon" aria-hidden="true"><i class="fa fa-comments-o"></i></span>
                <h2>Nie ma jeszcze dyskusji</h2>
                <p>Rozmowę możesz zacząć pod dowolnym artykułem.</p>
                <a class="portal-button" href="{{ route('home.latest') }}">Najnowsze artykuły</a>
            </div>
        @endif
    </div>
@endsection
