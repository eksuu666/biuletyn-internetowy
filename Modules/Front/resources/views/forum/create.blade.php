@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service forum">
        <a class="portal-back" href="{{ route('forum.sections.show', $section) }}">← {{ $details['name'] }}</a>
        <h1>Nowy temat</h1>
        <form class="service-card portal-service-copy forum-form" method="post" action="{{ route('forum.topics.store', $section) }}">
            @csrf
            <x-common-error-messages/>
            <label for="forum-title">Tytuł</label>
            <input class="form-control" id="forum-title" name="title" maxlength="120" value="{{ old('title') }}" required>
            <label for="forum-body">Treść</label>
            <textarea class="form-control" id="forum-body" name="body" rows="8" maxlength="5000" required>{{ old('body') }}</textarea>
            <button class="portal-button">Opublikuj temat</button>
        </form>
    </div>
@endsection
