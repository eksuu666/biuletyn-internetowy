@extends('front::layouts.master')

@section('content')
    <div @class(['portal-wrap', 'portal-list', 'portal-service' => request()->routeIs('search.index'), 'portal-news-list' => ! request()->routeIs('search.index')])>
        <div class="portal-list-header">
            @yield('list-heading', '')
            @unless($__env->hasSection('list-heading'))
                @include('front::partials.page-heading', ['title' => $heading, 'eyebrow' => request()->routeIs('search.index') ? 'Szukaj w sŁodziakach' : 'Czytaj sŁodziaki', 'icon' => request()->routeIs('search.index') ? 'search' : 'newspaper-o', 'description' => $intro ?? null])
            @endunless
            @yield('list-intro')
        </div>
        <div class="portal-page-content">

        @if($articles->isNotEmpty())
            <div class="portal-cards">
                @foreach($articles as $story)
                    @include('front::partials.portal-card')
                @endforeach
            </div>
        @else
            <p class="portal-empty">{{ $emptyMessage ?? 'Nie ma jeszcze opublikowanych materiałów w tym dziale.' }}</p>
        @endif

        <x-front-listing-page-pagination :paginator="$articles"/>
        </div>
    </div>
@endsection
