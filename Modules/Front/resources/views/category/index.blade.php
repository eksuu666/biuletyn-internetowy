@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list">
        <header class="portal-list-header">
            <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
            <h1>Wszystkie działy</h1>
        </header>
        <div class="portal-category-grid">
            @forelse($categories as $category)
                <a class="portal-category" href="{{ route('categories.show', $category->slug) }}">
                    <strong>{{ $category->name }}</strong>
                    <span>{{ trans_choice('{0} Brak artykułów|{1} :count artykuł|[2,4] :count artykuły|[5,*] :count artykułów', $category->articles_count) }}</span>
                    <span aria-hidden="true">↗</span>
                </a>
            @empty
                <p class="portal-empty">Działy pojawią się tutaj po dodaniu ich przez redakcję.</p>
            @endforelse
        </div>
        <x-front-listing-page-pagination :paginator="$categories"/>
    </div>
@endsection
