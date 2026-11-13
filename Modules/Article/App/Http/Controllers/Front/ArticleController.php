<?php

namespace Modules\Article\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Article\App\Models\Article;
use Modules\Article\App\Services\Front\ArticleReactionService;
use Modules\Article\App\Services\Front\ShareService;
use Modules\SEOManager\App\Services\Front\SEOService;

class ArticleController extends Controller
{
    public function __construct(
        private readonly SEOService $seoService,
        private readonly ShareService $shareService,
        private readonly ArticleReactionService $reactions,
    ) {}

    // For news
    public function showNews(Request $request, string $date, Article $article): View
    {
        abort_unless($article->type === Article::NEWS && $date === $article->published_at->format('Y/m/d'), 404);

        return $this->show($article, $request);
    }

    // For article
    public function show(Article $article, Request $request): View
    {
        abort_unless($article->isPubliclyVisible(), 404);
        $this->seoService->setArticlePageSEO($article);
        $shared_links = $this->shareService->generateSharedLinks($request->url(), $article->title);
        $previous_article = $article->previousArticle();
        $next_article = $article->nextArticle();
        $related_articles = $article->relatedArticles();
        visits($article)->increment();

        $reactions = $this->reactions->summary($article, $request);
        $threads = $article->publicCommentThreads();

        return view('front::single-article.show', compact('article', 'shared_links', 'previous_article', 'next_article', 'related_articles', 'reactions', 'threads'));
    }

    public function react(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->isPubliclyVisible(), 404);
        $validated = $request->validate(['reaction' => ['present', 'nullable', 'string', Rule::in(array_keys(ArticleReactionService::OPTIONS))]]);
        $this->reactions->set($article, $request, $validated['reaction']);

        return redirect($article->getUrl().'#reactions');
    }

    public function like(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->isPubliclyVisible(), 404);
        $this->reactions->set($article, $request, 'like');

        return redirect()->back()->with('success', __('article::messages.liked'));
    }

    public function unlike(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->isPubliclyVisible(), 404);
        $this->reactions->set($article, $request, null);

        return redirect()->back()->with('success', __('article::messages.unliked'));
    }
}
