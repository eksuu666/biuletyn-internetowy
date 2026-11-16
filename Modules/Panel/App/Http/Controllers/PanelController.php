<?php

namespace Modules\Panel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\Comment\App\Models\Comment;
use Modules\FileManager\App\Models\Image;
use Modules\Panel\App\Services\PanelService;
use Modules\Tag\App\Models\Tag;
use Modules\UserActivity\App\Models\RequestTrack;
use Modules\UserActivity\App\Models\UserTrack;

class PanelController extends Controller
{
    public function __construct(private readonly PanelService $panelService) {}

    public function __invoke(Request $request): View|RedirectResponse
    {
        if (! $request->user()->can(config('permissions_list.SUPER_ADMIN'))) {
            $section = $request->user()->can(config('permissions_list.ARTICLE_INDEX')) ? 'articles.index' : 'profile.edit';

            return to_route(config('app.panel_prefix').'.'.$section);
        }

        $dataCounts = $this->panelService->getDataCounts();
        $articles = $this->panelService->getLimitedData(Article::class, relations: ['hotness', 'image', 'category', 'tags']);
        $categories = $this->panelService->getLimitedData(Category::class, relations: ['image', 'category']);
        $tags = $this->panelService->getLimitedData(Tag::class, relations: ['hotness']);
        $images = $this->panelService->getLimitedData(Image::class);
        $imageClassName = Image::class;
        $comments = $this->panelService->getLimitedData(Comment::class);
        $articlesVisitsCount = $this->panelService->getArticlesVisitsCount();
        $visitsCount = RequestTrack::getVisitCounts();
        $visitorsCount = UserTrack::getVisitorCounts();

        return view('panel::index', compact(['dataCounts', 'articles', 'categories', 'tags', 'images', 'imageClassName',
            'comments', 'articlesVisitsCount', 'visitsCount', 'visitorsCount']));
    }
}
