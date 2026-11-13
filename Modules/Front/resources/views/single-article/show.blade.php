@extends('front::layouts.master')

@section('portal-topic', $article->category->category?->slug ?? $article->category->slug)

@section('content')
    @php($commentCount = $threads->sum(fn ($thread) => 1 + $thread['replies']->count()))
    <div class="portal-wrap portal-reading">
        <article>
            <header class="portal-story-heading">
                <a class="portal-kicker" href="{{ route('categories.show', $article->category->slug) }}">{{ $article->category->name }}</a>
                <h1>{{ $article->title }}</h1>
                <div class="portal-story-byline">
                    @if($article->user)
                        <a href="{{ route('author.index', $article->user->username) }}">
                            <img src="{{ $article->user->avatarUrl() }}" alt="" width="42" height="42">
                            {{ $article->user->full_name }}
                        </a>
                    @endif
                    <time datetime="{{ $article->published_at->toAtomString() }}">{{ $article->published_at->locale('pl')->translatedFormat('j F Y, H:i') }}</time>
                </div>
            </header>

            @if($article->image)
                <img class="portal-story-image" src="{{ asset('storage/' . $article->image->file_path) }}" alt="{{ $article->image->alt_text }}" fetchpriority="high">
            @endif

            <div class="portal-reading-grid">
                <div class="portal-reading-main">
                    <div class="portal-prose">
                        {!! app(\Modules\Article\App\Services\ContentSanitizer::class)->sanitize($article->body) !!}
                    </div>

                    @if($article->tags->isNotEmpty())
                        <nav class="portal-pills" aria-label="Tematy artykułu">
                            @foreach($article->tags->filter('status') as $tag)
                                <a href="{{ route('tags.show', $tag->slug) }}">{{ $tag->name }}</a>
                            @endforeach
                        </nav>
                    @endif

                    <div class="portal-story-actions" id="reactions">
                        <form class="portal-reactions" action="{{ route('news.react', $article->slug) }}" method="post" aria-label="Reakcje na artykuł">
                            @csrf
                            @method('patch')
                            @foreach(\Modules\Article\App\Services\Front\ArticleReactionService::OPTIONS as $key => [$icon, $label])
                                @php($selected = $reactions['selected'] === $key)
                                <button type="submit" name="reaction" value="{{ $selected ? '' : $key }}" aria-pressed="{{ $selected ? 'true' : 'false' }}" aria-label="{{ $label }} ({{ $reactions['counts'][$key] ?? 0 }})" title="{{ $label }}">
                                    <span aria-hidden="true">{{ $icon }}</span> {{ $reactions['counts'][$key] ?? 0 }}
                                </button>
                            @endforeach
                        </form>
                        <a href="#comments">Komentarze ({{ $commentCount }})</a>
                        @if(count($shared_links))
                            <details class="portal-share">
                                <summary>Udostępnij</summary>
                                <div class="portal-pills">
                                    @foreach($shared_links as $platform => $link)
                                        <a href="{{ $link }}" rel="noopener noreferrer">{{ ucfirst($platform) }}</a>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>

                    @if($article->user)
                        @include('front::partials.portal-author', ['profile' => $article->user])
                    @endif

                    @if($previous_article || $next_article)
                        <nav class="portal-story-neighbors" aria-label="Sąsiednie artykuły">
                            @if($previous_article)
                                <a href="{{ $previous_article->getUrl() }}"><span>← Poprzedni</span>{{ $previous_article->title }}</a>
                            @endif
                            @if($next_article)
                                <a href="{{ $next_article->getUrl() }}"><span>Następny →</span>{{ $next_article->title }}</a>
                            @endif
                        </nav>
                    @endif

                    <section id="comments" class="portal-comments" aria-labelledby="discussion-heading">
                        @include('front::single-article.partials.comments-area')
                    </section>
                </div>

                <aside class="portal-reading-aside">
                    @if($related_articles->isNotEmpty())
                        <section class="portal-brief" aria-labelledby="related-heading">
                            <h2 id="related-heading">Czytaj także</h2>
                            <ol>
                                @foreach($related_articles->take(4) as $related)
                                    <li><a href="{{ $related->getUrl() }}">{{ $related->title }}</a></li>
                                @endforeach
                            </ol>
                        </section>
                    @endif
                    @include('front::partials.portal-ads', ['placements' => $ads['first_sidebar']->concat($ads['second_sidebar'])])
                </aside>
            </div>
        </article>
    </div>
@endsection
