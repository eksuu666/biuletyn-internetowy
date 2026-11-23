@php
    $reactionComments = $threads->flatMap(fn ($thread) => collect([$thread['root']])->merge($thread['replies']->pluck('comment')));
    $commentReactions = app(\Modules\Comment\App\Services\Front\CommentReactionService::class)->summaries($reactionComments, request());
@endphp
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
