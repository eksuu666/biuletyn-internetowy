@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service">
        <header class="portal-list-header">
            <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
            <h1>Zapisane artykuły</h1>
            <p>Twoja lista w tej przeglądarce.</p>
        </header>
        <div data-saved-list="{{ route('saved.data') }}">
            <p class="portal-empty" role="status">Wczytuję listę…</p>
            <ul class="portal-forum-list service-card" hidden></ul>
        </div>
        <noscript><p>Włącz JavaScript, aby zobaczyć zapisane artykuły.</p></noscript>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('home/js/saved-articles.js') }}?v={{ filemtime(public_path('home/js/saved-articles.js')) }}" defer></script>
@endpush
