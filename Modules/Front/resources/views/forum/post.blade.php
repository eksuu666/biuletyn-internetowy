<article class="service-card portal-service-copy forum-post" id="wpis-{{ $post instanceof \Modules\Front\App\Models\ForumTopic ? 'temat' : $post->id }}">
    <header class="discussion-meta">
        <img src="{{ $post->user?->avatarUrl() ?? asset('seeders/images/profile_picture.jpg') }}" width="40" height="40" alt="">
        <div>
            <strong>{{ $post->user?->full_name ?? 'Były użytkownik' }}</strong>
            <time datetime="{{ $post->created_at->toAtomString() }}">{{ $post->created_at->locale('pl')->translatedFormat('j F Y, H:i') }}</time>
        </div>
    </header>
    <p>{{ $post->body }}</p>
    @can('comment::reject')
        <details class="forum-moderation">
            <summary>Usuń wpis</summary>
            <form action="{{ $removeUrl }}" method="post">
                @csrf
                @method('DELETE')
                <button class="portal-outline">Potwierdź usunięcie</button>
            </form>
        </details>
    @endcan
</article>
