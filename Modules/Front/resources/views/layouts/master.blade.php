<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if($site_details?->favicon)
        <link rel="icon" href="{{ $site_details->faviconLink() }}">
    @endif
    <x-feed-links/>
    {!! SEO::generate() !!}
    <link rel="stylesheet" href="{{ asset('home/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('home/css/portal.css') }}?v={{ filemtime(public_path('home/css/portal.css')) }}">
    @stack('styles')
</head>
<body class="news-portal">
    <a class="portal-skip" href="#main-content">Przejdź do treści</a>
    @include('front::partials.portal-header')
    <main id="main-content" class="portal-main">
        @yield('content')
    </main>
    @include('front::partials.portal-footer')
    @include('sweetalert::alert')
    @stack('scripts')
</body>
</html>
