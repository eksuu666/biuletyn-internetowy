<div class="author-box">
    <div class="author-img pull-left">
        <img src="{{ $author->avatarUrl() }}" alt="{{ $author->full_name }}" loading="lazy">
    </div>
    <div class="author-info">
        <h3>{{ $author->full_name }}</h3>
        <div class="author-counter pull-right">
            <span>{{ $articlesCount }} materiałów</span>
            <span>{{ $commentsCount }} komentarzy</span>
        </div>
        <p><strong>{{ $author->publicRoleName() }}</strong></p>
        <p>{{ $author->bio ?: 'Autor redakcji sŁodziaka.' }}</p>
        @if(!empty($author?->socialNetworks) && $author?->socialNetworks->count() > 0)
            <div class="authors-social" style="clear: both">
                <span>Obserwuj autora: </span>
                @foreach($author?->socialNetworks as $socialNetwork)
                    <a href="{{ $socialNetwork->url }}" title="{{ $socialNetwork->name }}"><i class="fa fa-{{ $socialNetwork->name }}"></i></a>
                @endforeach
            </div>
        @endif
    </div>
</div> <!-- Author box end -->
