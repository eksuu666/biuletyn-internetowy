<?php

namespace Modules\Front\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\SEOManager\App\Services\Front\SEOService;

class SearchController extends Controller
{
    public function __construct(private readonly SEOService $SEOService) {}

    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'query' => 'nullable|string|max:200',
            'text' => 'nullable|string|max:200',
            'category' => 'nullable|string|max:255',
            'days' => ['nullable', Rule::in(['7', '30', '365'])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest'])],
        ]);
        $searchText = trim($filters['query'] ?? $filters['text'] ?? '');
        $category = $filters['category'] ?? null;
        $days = (int) ($filters['days'] ?? 0);
        $sort = $filters['sort'] ?? 'newest';
        $this->SEOService->setSearchPageSEO($searchText);
        $categories = Category::active()->orderBy('name')->get(['name', 'slug']);
        $articles = Article::search($searchText)
            ->query(fn (Builder $query) => $query->publiclyVisible()->with(['category', 'image', 'user'])
                ->when($category !== null, fn (Builder $query) => $query->whereRelation('category', 'slug', $category))
                ->when($days, fn (Builder $query) => $query->where('published_at', '>=', now()->subDays($days)))
                ->orderBy('published_at', $sort === 'oldest' ? 'asc' : 'desc')
                ->orderByDesc('id'))
            ->paginate(10)->withQueryString();

        return view(
            'front::search.index',
            compact('searchText', 'articles', 'categories', 'category', 'days', 'sort')
        );
    }
}
