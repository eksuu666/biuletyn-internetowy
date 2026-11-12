<?php

namespace Modules\Home\App\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Article\App\Models\Article;

class HomeService
{
    public function baseQuery(): Builder
    {
        return Article::query()
            ->publiclyVisible()
            ->with(['image', 'category', 'user'])
            ->latest('published_at')
            ->orderByDesc('id');
    }
}
