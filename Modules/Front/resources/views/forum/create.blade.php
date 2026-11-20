@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-static portal-service forum">
        @include('front::partials.page-heading', ['title' => 'Nowy temat', 'parentLabel' => $details['name'], 'parentUrl' => route('forum.sections.show', $section), 'eyebrow' => 'Forum sŁodziaków', 'icon' => 'pencil'])
        <div class="portal-page-content">
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
    </div>
@endsection
