<div class="discussion-heading">
    <h2 id="discussion-heading">Komentarze <span>{{ $commentCount }}</span></h2>
    <a href="#discussion-new">Dodaj komentarz</a>
</div>

@if(session('discussion_status'))
    <p class="discussion-status" role="status">{{ session('discussion_status') }}</p>
@endif

@forelse($threads as $thread)
    <div class="discussion-thread">
        @include('front::single-article.partials.comment-card', ['comment' => $thread['root'], 'parent' => null])
        @if($thread['replies']->isNotEmpty())
            <div class="discussion-replies" aria-label="Odpowiedzi">
                @foreach($thread['replies'] as $reply)
                    @include('front::single-article.partials.comment-card', $reply)
                @endforeach
            </div>
        @endif
    </div>
@empty
    <p class="discussion-empty">Nie ma jeszcze komentarzy. Dodaj pierwszy.</p>
@endforelse

<div id="discussion-new" class="discussion-new">
    <h3>Dodaj komentarz</h3>
    @include('front::single-article.partials.comment-form', ['formKey' => 'new', 'action' => route('comments.store'), 'button' => 'Opublikuj komentarz'])
</div>
