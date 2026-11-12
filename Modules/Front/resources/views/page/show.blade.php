@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>{{ $page->title }}</h1>
        <div class="portal-prose portal-static-content">{!! $page->content !!}</div>
    </div>
@endsection
