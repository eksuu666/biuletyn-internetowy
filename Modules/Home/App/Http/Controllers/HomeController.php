<?php

namespace Modules\Home\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\View\View;
use Modules\Home\App\Services\HomeService;
use Modules\PageBuilder\App\Models\Page;
use Modules\SEOManager\App\Services\Front\SEOService;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService, private readonly SEOService $seoService) {}

    public function __invoke(): View
    {
        $this->seoService->setHomePageSEO();

        $latest = $this->homeService->baseQuery()->limit(5)->get();
        $featured = $this->homeService->baseQuery()->editorChoice()->first() ?? $latest->first();
        $latest = $latest->where('id', '!=', $featured?->id)->take(4);
        $guides = $this->homeService->baseQuery()
            ->whereHas('category', fn ($query) => $query->where('slug', 'poradniki'))
            ->limit(3)->get();
        $adoption = $this->homeService->baseQuery()
            ->whereHas('category', fn ($query) => $query->where('slug', 'adopcje'))
            ->first();
        $bulletin = Page::query()->active()->where('slug', 'biuletyn')->first();

        return view('home::index', compact('featured', 'latest', 'guides', 'adoption', 'bulletin'));
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
