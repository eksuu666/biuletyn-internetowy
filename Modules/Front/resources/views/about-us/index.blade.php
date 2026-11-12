@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static">
        <a class="portal-back" href="{{ route('home.index') }}">← Strona główna</a>
        <h1>{{ $about->title }}</h1>
        <div class="portal-prose portal-static-content">{!! $about->content !!}</div>
    </div>
@endsection
