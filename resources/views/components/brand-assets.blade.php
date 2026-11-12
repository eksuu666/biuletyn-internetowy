@props(['site' => null])

<link rel="icon" href="{{ $site?->faviconLink() ?? asset('brand/slodziaki.jpg') }}">
<link rel="stylesheet" href="{{ asset('brand/identity.css') }}?v={{ filemtime(public_path('brand/identity.css')) }}">
