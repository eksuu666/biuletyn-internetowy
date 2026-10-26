<?php

namespace Modules\Front\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Article\App\Models\Article;
use Modules\SEOManager\App\Services\Front\SEOService;

class SearchController extends Controller
{
    public function __construct(private readonly SEOService $SEOService) {}

    public function __invoke(Request $request): View
    {
        $request->validate(['query' => 'nullable|string|max:200', 'text' => 'nullable|string|max:200']);
        $searchText = trim((string) $request->get('query', $request->get('text', '')));
        $this->SEOService->setSearchPageSEO($searchText);
        $articles = Article::search($searchText)
            ->query(fn (Builder $query) => $query->publiclyVisible()->with(['category', 'image', 'approvedComments', 'user'])->latest('published_at'))
            ->paginate(10)->withQueryString();
        if (! $searchText) {
            $searchText = __('all');
        }

        return view(
            'front::search.index',
            compact(['searchText', 'articles'])
        );
    }
}
