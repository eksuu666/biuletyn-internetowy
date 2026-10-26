<div class="author-box">
    <div class="author-img pull-left">
        <a href="{{ route('author.index', $article->user->username) }}">
            <img src="{{ $article->user->avatarUrl() }}" alt="{{ $article->user->full_name }}" loading="lazy">
        </a>
    </div>
    <div class="author-info">
        <h3>
            <a class="" href="{{ route('author.index', $article->user->username) }}">{{ $article->user->full_name }}</a>
        </h3>
        <p>{{ $article->user->publicRoleName() }}</p>
        <p>{{ $article->user->bio ?: 'Autor redakcji sŁodziaka.' }}</p>
        @if(!empty($article->user?->socialNetworks) && $article->user?->socialNetworks->count() > 0)
            <div class="authors-social">
                <span>Obserwuj autora: </span>
                @foreach($article->user->socialNetworks as $socialNetwork)
                    <a href="{{ $socialNetwork->url }}" title="{{ $socialNetwork->name }}"><i class="fa fa-{{ $socialNetwork->name }}"></i></a>
                @endforeach
            </div>
        @endif
    </div>
</div> <!-- Author box end -->
