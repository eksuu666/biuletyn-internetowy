@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service">
        @include('front::partials.page-heading', ['title' => $page->title, 'eyebrow' => 'sŁodziaki', 'icon' => 'file-text-o'])
        <div class="portal-prose service-card portal-service-copy">{!! $page->content !!}</div>
    </div>
@endsection
