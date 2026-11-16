<?php

namespace Modules\Home\App\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Article\App\Models\Article;
use Modules\UserActivity\App\Models\RequestTrack;

class HomeService
{
    public function baseQuery(): Builder
    {
        return Article::query()
            ->publiclyVisible()
            ->with(['image', 'category', 'user'])
            ->latest('published_at')
            ->orderByDesc('id');
    }

    public function popularQuery(): Builder
    {
        $reads = RequestTrack::query()->where('tag', RequestTrack::PAGE_VIEW)
            ->where('created_at', '>=', now()->subDays(7))->whereNotNull('article_id')
            ->groupBy('article_id')->selectRaw('article_id, COUNT(DISTINCT user_track_id) AS readers');

        return $this->baseQuery()
            ->joinSub($reads, 'recent_reads', 'recent_reads.article_id', '=', 'articles.id')
            ->select('articles.*')->reorder()->orderByDesc('recent_reads.readers')
            ->orderByDesc('articles.published_at')->orderByDesc('articles.id');
    }
}
