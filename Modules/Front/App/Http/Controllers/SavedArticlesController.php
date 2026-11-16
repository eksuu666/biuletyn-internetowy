<?php

namespace Modules\Front\App\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Article\App\Models\Article;

class SavedArticlesController
{
    public function index(): View
    {
        SEOTools::setTitle('Zapisane artykuły');

        return view('front::saved-articles');
    }

    public function data(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => 'required|array|max:50',
            'ids.*' => 'required|integer|min:1|distinct',
        ]);

        $articles = Article::publiclyVisible()->whereKey($data['ids'])->with('category')->get()
            ->map(fn (Article $article) => [
                'id' => (string) $article->id,
                'title' => $article->title,
                'url' => $article->getUrl(),
                'category' => $article->category->name,
            ]);

        return response()->json($articles);
    }
}
