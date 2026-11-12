@extends('front::article-list', ['emptyMessage' => 'Ten autor nie ma jeszcze opublikowanych tekstów.'])

@section('list-heading')
    @include('front::partials.portal-author', ['profile' => $author, 'fullProfile' => true])
@endsection
