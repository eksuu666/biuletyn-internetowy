@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list">
        <header class="portal-list-header">
            <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
            @yield('list-heading', '')
            @unless($__env->hasSection('list-heading'))
                <h1>{{ $heading }}</h1>
            @endunless
            @yield('list-intro')
        </header>

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
@endsection
