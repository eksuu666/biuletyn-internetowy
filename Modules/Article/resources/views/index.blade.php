@extends('panel::layouts.master', ['title' => 'Artykuły'])

@section('content')
    <div class="editorial-workspace">
        <header class="editorial-page-heading">
            <h1>Artykuły</h1>
            @can(config('permissions_list.ARTICLE_STORE'))
                <a class="btn btn-primary" href="{{ route(config('app.panel_prefix').'.articles.create') }}">Nowy artykuł</a>
            @endcan
        </header>
        <form class="editorial-card editorial-filters" method="get" aria-label="Filtry artykułów">
            <div>
                <label for="article-query">Szukaj artykułów</label>
                <input id="article-query" name="query" type="search" class="form-control" value="{{ request('query') }}">
            </div>
            <div>
                <label for="article-stage">Etap</label>
                <select id="article-stage" name="stage" class="form-select">
                    <option value="">Wszystkie etapy</option>
                    @foreach(\Modules\Article\App\Models\Article::WORKFLOW_LABELS as $stage => $label)
                        <option value="{{ $stage }}" @selected(request('stage') === $stage)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-outline-secondary" type="submit">Filtruj</button>
        </form>
        <div class="editorial-card editorial-articles">
            @forelse($articles as $article)
                @php($canEdit = app(\Modules\Article\App\Services\EditorialWorkflow::class)->canEdit(auth()->user(), $article))
                <article class="editorial-article-row">
                    <div>
                        <h2><a href="{{ route(config('app.panel_prefix').'.articles.review', $article) }}">{{ $article->title }}</a></h2>
                        <p>{{ collect([$article->category?->name, $article->user?->full_name])->filter()->implode(' · ') }}</p>
                        <details class="editorial-advanced">
                            <summary>Szczegóły</summary>
                            <dl class="editorial-details">
                                <dt>Adres</dt><dd>{{ $article->slug }}</dd>
                                <dt>Rodzaj</dt><dd>{{ __('article::types.' . $article->type) }}</dd>
                                <dt>Tagi</dt><dd>{{ $article->tagNames() ?: '—' }}</dd>
                                <dt>Utworzono</dt><dd>{{ panel_date_format($article->created_at) }}</dd>
                                <dt>Polubienia</dt><dd>{{ $article->likeCount }}</dd>
                                <dt>Wybór redakcji</dt><dd>{{ $article->editor_choice ? 'Tak' : 'Nie' }}</dd>
                                <dt>Pilne</dt><dd>{{ $article->isHot() ? 'Tak' : 'Nie' }}</dd>
                            </dl>
                        </details>
                    </div>
                    <div>
                        <span @class(['editorial-stage', 'editorial-stage-live' => $article->workflow_state === 'published' && !$article->published_at->isFuture()])>
                            {{ $article->workflow_state === 'published' && $article->published_at->isFuture() ? 'Zaplanowany' : \Modules\Article\App\Models\Article::WORKFLOW_LABELS[$article->workflow_state] }}
                        </span>
                        <p>{{ panel_date_format($article->published_at) }}</p>
                    </div>
                    <div class="editorial-row-actions">
                        @if($canEdit && auth()->user()->can(config('permissions_list.ARTICLE_UPDATE')))
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route(config('app.panel_prefix').'.articles.edit', $article) }}">Edytuj</a>
                            @can(config('permissions_list.SEO_MANAGEMENT'))
                                <x-seo-manager-seo-settings-button :route="route(config('app.panel_prefix').'.articles.seo-settings', $article)"/>
                            @endcan
                        @endif
                        @if($canEdit && auth()->user()->can(config('permissions_list.ARTICLE_DESTROY')))
                            <x-common-delete-button :route="route(config('app.panel_prefix').'.articles.destroy', $article)"/>
                        @endif
                    </div>
                </article>
            @empty
                <p class="editorial-empty">Brak artykułów do wyświetlenia.</p>
            @endforelse
            @if($articles->hasPages())
                <div class="editorial-pagination">{{ $articles->links() }}</div>
            @endif
        </div>
    </div>
@endsection
