<?php

namespace Modules\Front\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\SEOManager\App\Services\Front\SEOService;
use Modules\Tag\App\Models\Tag;

class TagController extends Controller
{
    public function __construct(private readonly SEOService $SEOService) {}

    public function show(Tag $tag): View
    {
        abort_unless($tag->status, 404);
        $this->SEOService->setTagPageSEO($tag);
        $articles = $tag->articles()->publiclyVisible()->with(['category', 'image', 'approvedComments', 'user'])->latest('published_at')->paginate(10);

        return view(
            'front::tag.index',
            compact(['tag', 'articles'])
        );
    }
}
