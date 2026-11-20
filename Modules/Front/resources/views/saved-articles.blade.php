@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service">
        @include('front::partials.page-heading', ['title' => 'Zapisane artykuły', 'eyebrow' => 'Do przeczytania', 'icon' => 'bookmark-o', 'description' => 'Twoja lista w tej przeglądarce.'])
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
