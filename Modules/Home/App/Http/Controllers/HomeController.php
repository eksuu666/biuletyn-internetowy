<?php

namespace Modules\Home\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\View\View;
use Modules\Home\App\Services\HomeService;
use Modules\SEOManager\App\Services\Front\SEOService;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService, private readonly SEOService $seoService) {}

    public function __invoke(): View
    {
        $this->seoService->setHomePageSEO();

        $latest = $this->homeService->baseQuery()->limit(13)->get();
        $featured = $this->homeService->baseQuery()->editorChoice()->first() ?? $latest->first();
        $stories = $latest->where('id', '!=', $featured?->id)->values();

        return view('home::index', compact('featured', 'latest', 'stories'));
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
}
