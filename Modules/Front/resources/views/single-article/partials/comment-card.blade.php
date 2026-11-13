<article class="discussion-comment" id="comment-{{ $comment->id }}" tabindex="-1" aria-labelledby="comment-author-{{ $comment->id }}">
    <header class="discussion-meta">
        <img src="{{ $comment->commenterImageLink() }}" alt="" width="40" height="40" loading="lazy">
        <div>
            <strong id="comment-author-{{ $comment->id }}">{{ $comment->commenterName() }}</strong>
            @if($comment->isGuest()) <span class="discussion-guest">Gość</span> @endif
            <time datetime="{{ $comment->created_at->toAtomString() }}">{{ $comment->created_at->locale('pl')->diffForHumans() }}</time>
        </div>
    </header>
    @if($parent)
        <a class="discussion-parent" href="#comment-{{ $parent->id }}">W odpowiedzi do: {{ $parent->commenterName() }}</a>
    @endif
    <div class="discussion-content">
        <x-markdown>{{ $comment->comment }}</x-markdown>
    </div>
    <div class="discussion-actions">
        <details @if(old('discussion_form') === 'reply-'.$comment->id && $errors->discussion->any()) open @endif>
            <summary>Odpowiedz</summary>
            @include('front::single-article.partials.comment-form', ['formKey' => 'reply-'.$comment->id, 'action' => route('comments.reply', $comment), 'button' => 'Opublikuj odpowiedź'])
        </details>
        @can('update', $comment)
            <details @if(old('discussion_form') === 'edit-'.$comment->id && $errors->discussion->any()) open @endif>
                <summary>Edytuj</summary>
                @include('front::single-article.partials.comment-form', ['formKey' => 'edit-'.$comment->id, 'action' => route('comments.update', $comment), 'button' => 'Zapisz zmiany', 'editingComment' => $comment])
            </details>
        @endcan
        @can('delete', $comment)
            <details>
                <summary>Usuń</summary>
                <form action="{{ route('comments.destroy', $comment) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <p>Usunięcie komentarza usunie również odpowiedzi.</p>
                    <button type="submit" class="portal-outline">Potwierdź usunięcie</button>
                </form>
            </details>
        @endcan
    </div>
</article>
