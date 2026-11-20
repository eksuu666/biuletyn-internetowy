<?php

namespace Modules\Home\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Home\App\Models\BulletinIssue;
use Modules\Home\App\Services\HomeService;
use Modules\Home\App\Services\HomeFeedService;
use Modules\SEOManager\App\Services\Front\SEOService;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService, private readonly SEOService $seoService) {}

    public function __invoke(): View
    {
        $this->seoService->setHomePageSEO();

        $feed = app(HomeFeedService::class)->batch();
        $popular = $this->homeService->popularQuery()->limit(4)->get();
        $bulletinIssues = BulletinIssue::published()->orderByDesc('published_on')->orderByDesc('id')->limit(3)->get();

        return view('home::index', compact('feed', 'popular', 'bulletinIssues'));
    }

    public function feed(Request $request): JsonResponse
    {
        $data = $request->validate(['cursor' => 'required|string|max:8192']);
        $feed = app(HomeFeedService::class)->batch($data['cursor']);

        return response()->json(['html' => view('home::feed.items', $feed)->render(), 'nextUrl' => $feed['nextUrl']]);
    }

    public function latest(): View
    {
        SEOTools::setTitle('Najnowsze wiadomości');
        SEOTools::setDescription('Najnowsze wiadomości, porady i historie o zwierzętach.');
        $articles = $this->homeService->baseQuery()->paginate(12);
        SEOTools::setCanonical($articles->url($articles->currentPage()));

        return view('front::article-list', [
            'heading' => 'Najnowsze',
            'articles' => $articles,
            'emptyMessage' => 'Pierwsze wiadomości pojawią się tutaj po publikacji.',
        ]);
    }

    public function popular(): View
    {
        $heading = 'Najczęściej czytane';
        SEOTools::setTitle($heading);
        $top = $this->homeService->popularQuery()->limit(10)->get();
        $articles = new LengthAwarePaginator($top, $top->count(), 10, 1, ['path' => route('home.popular')]);
        SEOTools::setCanonical(route('home.popular'));

        return view('front::article-list', [
            'heading' => $heading,
            'intro' => 'Top 10 - Z ostatnich 7 dni',
            'articles' => $articles,
            'emptyMessage' => 'Ranking pojawi się po zebraniu pierwszych odsłon.',
        ]);
    }
}
