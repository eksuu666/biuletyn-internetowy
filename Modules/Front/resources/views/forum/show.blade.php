@extends('front::layouts.master')

@section('content')
    <div class="portal-wrap portal-list portal-service forum">
        @include('front::partials.page-heading', ['title' => $topic->title, 'parentLabel' => 'Forum', 'parentUrl' => route('forum.index'), 'eyebrow' => $details['name'], 'icon' => 'comments-o'])
        <div class="portal-page-content">
        @include('front::forum.post', ['post' => $topic, 'removeUrl' => route('forum.topics.destroy', $topic)])

        <section class="forum-recent" aria-labelledby="forum-replies-heading">
            <h2 id="forum-replies-heading">Odpowiedzi <span>{{ $replies->total() }}</span></h2>
            @foreach($replies as $reply)
                @include('front::forum.post', ['post' => $reply, 'removeUrl' => route('forum.replies.destroy', [$topic, $reply])])
            @endforeach
            <x-front-listing-page-pagination :paginator="$replies"/>

            @auth
                <form class="service-card portal-service-copy forum-form" id="odpowiedz" action="{{ route('forum.topics.reply', $topic) }}" method="post">
                    @csrf
                    <x-common-error-messages/>
                    <label for="reply-body">Twoja odpowiedź</label>
                    <textarea class="form-control" name="body" id="reply-body" rows="5" maxlength="5000" required>{{ old('body') }}</textarea>
                    <button class="portal-button">Opublikuj odpowiedź</button>
                </form>
            @else
                <a class="portal-button" href="{{ route('forum.topics.reply-form', $topic) }}">Zaloguj się, aby odpowiedzieć</a>
            @endauth
        </section>
        </div>
    </div>
@endsection
