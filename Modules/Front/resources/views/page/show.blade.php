@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>{{ $page->title }}</h1>
        <div class="portal-prose service-card portal-service-copy">{!! $page->content !!}</div>
    </div>
@endsection
