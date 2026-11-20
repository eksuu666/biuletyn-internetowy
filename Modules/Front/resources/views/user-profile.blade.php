@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-member-profile">
        <a class="portal-back" href="{{ route('home.index') }}">Portal</a>
        <section class="portal-author" aria-label="Profil użytkownika">
            <img src="{{ $profile->avatarUrl() }}" alt="" width="96" height="96">
            <div><h1>{{ $profile->full_name }}</h1>@if($profile->bio)<p>{{ $profile->bio }}</p>@endif</div>
        </section>
    </div>
@endsection
