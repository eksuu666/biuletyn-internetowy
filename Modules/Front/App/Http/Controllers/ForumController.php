<?php

namespace Modules\Front\App\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Modules\Article\App\Models\Article;

class ForumController
{
    public function __invoke(): View
    {
        $topLevel = fn (Builder $query) => $query->whereNull('parent_id');
        $articles = Article::publiclyVisible()
            ->whereHas('approvedComments', $topLevel)
            ->withMax(['approvedComments as latest_discussion_at' => $topLevel], 'created_at')
            ->with('category')
            ->orderByDesc('latest_discussion_at')->orderByDesc('id')
            ->paginate(15);

        SEOTools::setTitle('Forum');
        SEOTools::setDescription('Dyskusje czytelników pod artykułami sŁodziaków.');
        SEOTools::setCanonical($articles->url($articles->currentPage()));

        return view('front::forum.index', compact('articles'));
    }
}
