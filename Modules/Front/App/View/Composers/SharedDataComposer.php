<?php

namespace Modules\Front\App\View\Composers;

use Illuminate\View\View;
use Modules\AdManager\App\Models\Ad;
use Modules\ContactUs\App\Models\ContactInfo;
use Modules\Front\App\Services\StaticContentService;
use Modules\Setting\App\Models\SiteDetail;
use Modules\Setting\App\Services\SocialNetworkService;

class SharedDataComposer
{
    public function __construct(
        private readonly StaticContentService $staticContentService,
        private readonly SocialNetworkService $socialNetworkService
    ) {}

    public function compose(View $view): void
    {
        $socialNetworks = (function () {
            return $this->socialNetworkService->getSocialNetworksWithTag(SocialNetworkService::TAG);
        })();

        $siteDetails = (static function () {
            return SiteDetail::with('mainLogo', 'secondLogo', 'favicon')->latest()->first();
        })();

        $contactInfo = (static function () {
            return ContactInfo::query()->first(['email', 'phone']);
        })();

        $ads = (static function () {
            return [
                'header' => Ad::active()->bySection(Ad::HEADER)->first(),
                'first_sidebar' => Ad::active()->bySection(Ad::FIRST_SIDEBAR)->limit(4)->get(),
                'second_sidebar' => Ad::active()->bySection(Ad::SECOND_SIDEBAR)->limit(10)->get(),
                'first_section' => Ad::active()->bySection(Ad::FIRST_SECTION)->limit(10)->get(),
                'second_section' => Ad::active()->bySection(Ad::SECOND_SECTION)->limit(10)->get(),
                'third_section' => Ad::active()->bySection(Ad::THIRD_SECTION)->limit(10)->get(),
                'fourth_section' => Ad::active()->bySection(Ad::FOURTH_SECTION)->limit(10)->get(),
            ];
        })();

        $viewData = $this->staticContentService->composeViewData();
        $viewData['social_networks'] = $socialNetworks;
        $viewData['site_details'] = $siteDetails;
        $viewData['contact_info'] = $contactInfo;
        $viewData['ads'] = $ads;
        $view->with($viewData);
    }
}
