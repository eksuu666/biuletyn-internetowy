<div class="discussion-heading">
    <h2 id="discussion-heading" tabindex="-1">Komentarze <span data-comment-count>{{ $commentCount }}</span></h2>
    <a href="#discussion-new">Dodaj komentarz</a>
</div>

<p class="discussion-status" role="status" data-discussion-status>{{ session('discussion_status') }}</p>

<div data-comment-threads>
    @include('front::single-article.partials.comment-threads')
</div>

<div id="discussion-new" class="discussion-new">
    <h3>Dodaj komentarz</h3>
    @include('front::single-article.partials.comment-form', ['formKey' => 'new', 'action' => route('comments.store'), 'button' => 'Opublikuj komentarz'])
</div>
