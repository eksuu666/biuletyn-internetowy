@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service forum">
        @include('front::partials.page-heading', ['title' => $details['name'], 'parentLabel' => 'Forum', 'parentUrl' => route('forum.index'), 'description' => $details['intro'], 'eyebrow' => 'Forum sŁodziaków', 'icon' => 'comments-o', 'headingImage' => 'home/forum/'.$section.'-rysunek.webp'])
        <div class="portal-page-content">
        <div class="portal-content-heading"><h2>Tematy w dziale</h2><a class="portal-button" href="{{ route('forum.topics.create', $section) }}"><i class="fa fa-plus" aria-hidden="true"></i>Załóż temat</a></div>
        @if($topics->isNotEmpty())
            @include('front::forum.topic-list')
            <x-front-listing-page-pagination :paginator="$topics"/>
        @else
            <p class="service-card portal-service-copy">W tym dziale nie ma jeszcze tematów. Możesz założyć pierwszy.</p>
        @endif
        </div>
    </div>
@endsection
