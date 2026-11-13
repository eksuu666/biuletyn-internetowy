<?php

namespace Modules\Front\App\View\Composers;

use Illuminate\View\View;
use Modules\AdManager\App\Models\Ad;
use Modules\Category\App\Models\Category;
use Modules\Front\App\Services\StaticContentService;
use Modules\PageBuilder\App\Models\Page;
use Modules\Setting\App\Models\SiteDetail;
use Modules\Setting\App\Services\SocialNetworkService;

class SharedDataComposer
{
    public const PORTAL_TOPICS = [
        'aktualnosci' => 'Aktualności',
        'adopcje' => 'Adopcje',
        'poradniki' => 'Poradniki',
        'reportaze' => 'Reportaże',
    ];

    public function __construct(
        private readonly StaticContentService $staticContentService,
        private readonly SocialNetworkService $socialNetworkService,
    ) {}

    public function compose(View $view): void
    {
        if ($view->name() === 'front::layouts.master') {
            $view->with($this->staticContentService->composeViewData() + [
                'site_details' => SiteDetail::with(['mainLogo', 'favicon'])->latest()->first(),
                'portal_topics' => Category::query()->active()
                    ->whereIn('slug', array_keys(self::PORTAL_TOPICS))->pluck('slug', 'slug'),
                'bulletin_page' => Page::query()->active()->where('slug', 'biuletyn')->first(),
                'social_networks' => $this->socialNetworkService->getSocialNetworksWithTag(SocialNetworkService::TAG),
            ]);

            return;
        }

        $sections = $view->name() === 'home::index'
            ? ['header' => Ad::HEADER, 'first_section' => Ad::FIRST_SECTION, 'second_section' => Ad::SECOND_SECTION, 'third_section' => Ad::THIRD_SECTION, 'fourth_section' => Ad::FOURTH_SECTION, 'first_sidebar' => Ad::FIRST_SIDEBAR, 'second_sidebar' => Ad::SECOND_SIDEBAR]
            : ['first_sidebar' => Ad::FIRST_SIDEBAR, 'second_sidebar' => Ad::SECOND_SIDEBAR];
        $ads = [];
        foreach ($sections as $key => $section) {
            $ads[$key] = Ad::with('image')->active()->bySection($section)->limit($key === 'header' ? 1 : 10)->get();
        }

        $view->with('ads', $ads);
    }
}
