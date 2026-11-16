@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list">
        <header class="portal-list-header">
            <h1>Forum</h1>
            <p>Dyskusje pod artykułami sŁodziaków.</p>
        </header>

        @if($articles->isNotEmpty())
            <ul class="portal-forum-list">
                @foreach($articles as $story)
                    <li>
                        <div>
                            <a class="portal-kicker" href="{{ route('categories.show', $story->category->slug) }}">{{ $story->category->name }}</a>
                            <h2><a href="{{ $story->getUrl() }}#comments">{{ $story->title }}</a></h2>
                        </div>
                        <a class="portal-outline" href="{{ $story->getUrl() }}#comments" aria-label="Dołącz do dyskusji: {{ $story->title }}">Dołącz do dyskusji <span aria-hidden="true">→</span></a>
                    </li>
                @endforeach
            </ul>
            <x-front-listing-page-pagination :paginator="$articles"/>
        @else
            <p class="portal-empty">Nie ma jeszcze dyskusji. Możesz rozpocząć rozmowę pod dowolnym artykułem.</p>
            <a class="portal-button" href="{{ route('home.latest') }}">Najnowsze artykuły</a>
        @endif
    </div>
@endsection
