<?php

namespace Modules\Front\App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\MenuBuilder\App\Models\Menu;
use Modules\Tag\App\Models\Tag;

class StaticContentService
{
    // Read publication-sensitive lists afresh so embargoes and withdrawals apply immediately.
    private function fresh(\Closure $query): mixed
    {
        return $query();
    }

    public function composeViewData(): array
    {
        $trending_bar = [
            'hot_articles' => $this->getHotArticles(),
        ];

        $main_nav = [
            'menus' => $this->getMenus(),
            'categories' => $this->getCategories(),
        ];

        $first_sidebar = [
            'articles_with_most_visits' => $this->getArticlesWithMostVisit(),
        ];

        $second_sidebar = [
            'latest_tags' => $this->getLatestTags(),
        ];

        $footer = [
            'editor_choices' => $this->getEditorChoices(),
            'hot_topics' => $this->getHotTopics(),
            'articles_with_most_comments' => $this->getArticleWithMostComments(),
        ];

        return compact(['trending_bar', 'main_nav', 'second_sidebar', 'first_sidebar', 'footer']);
    }

    public function getHotArticles(): Collection
    {
        return $this->fresh(function () {
            $articles = $this->baseQuery()
                ->whereHas('hotness', function ($query) {
                    $query->where('is_hot', true);
                })
                ->limit(15)
                ->get();

            if ($articles->count() <= 0) {
                $articles = $this->baseQuery()->limit(10)->get();
            }

            return $articles;
        });
    }

    private function baseQuery(): Builder
    {
        return Article::with(['hotness', 'image', 'category', 'tags', 'user'])->latest('published_at')->publiclyVisible();
    }

    public function getMenus(): Collection
    {
        return $this->fresh(static function () {
            $mainMenus = Menu::mainMenus()->active()->get()->pluck('id')->toArray();
            $mainMenusWithChildren = Menu::mainMenusWithChildren()->active()->get()->pluck('id')->toArray();
            $categoryMenus = Menu::categoryMenus()->active()->get()->pluck('id')->toArray();
            $parentCategoryMenus = Menu::parentCategoryMenus()->active()->get()->pluck('id')->toArray();
            $ignoreIds = array_merge($mainMenus, $mainMenusWithChildren, $categoryMenus, $parentCategoryMenus);

            $menus = Menu::with(['parent', 'children', 'category'])
                ->whereIn('id', $ignoreIds)
                ->latest('position')
                ->active()
                ->get();

            // Set limit for each category articles
            $menus->each(function ($menu) {
                if ($menu?->category !== null) {
                    $menu->category->setRelation('articles', $menu->category->articles()->publiclyVisible()->latest('published_at')->limit(4)->get());

                    $menu->category->setRelation('categories', $menu->category->categories()->active()->limit(5)->get());
                    $menu->category->categories->each(function ($childCategory) {
                        $childCategory->setRelation('articles', $childCategory->articles()->publiclyVisible()->latest('published_at')->limit(4)->get());
                    });
                }
            });

            return $menus;
        });
    }

    public function getCategories(): Collection
    {
        return $this->fresh(function () {
            return Category::query()
                ->whereHas('articles', function (Builder $query) {
                    $query->active()->published();
                })
                ->parentCategories()
                ->withCount('articles')
                ->latest('articles_count')
                ->active()
                ->get();
        });
    }

    public function getArticlesWithMostVisit(): array
    {
        return $this->fresh(function () {
            $mostVisits = visits(Article::class)->top(20)->filter(fn (Article $article) => $article->isPubliclyVisible())->take(4);
            if ($mostVisits->count() === 0) {
                $mostVisits = $this->baseQuery()->limit(4)->get();
            }
            $firstArticle = $mostVisits->shift();

            return [
                'first' => $firstArticle,
                'others' => $mostVisits,
            ];
        });
    }

    public function getLatestTags(): Collection
    {
        return $this->fresh(function () {
            return Tag::active()->whereHas('articles', fn ($query) => $query->publiclyVisible())->latest()->limit(30)->get();
        });
    }

    public function getEditorChoices(): Collection
    {
        return $this->fresh(function () {
            return $this->baseQuery()->editorChoice()->limit(3)->get();
        });
    }

    public function getHotTopics(): Collection
    {
        return $this->fresh(function () {
            return Tag::active()->with('hotness')
                ->whereHas('hotness', function ($query) {
                    $query->where('is_hot', true);
                })
                ->withCount('articles')
                ->whereHas('articles', fn ($query) => $query->publiclyVisible())
                ->latest()
                ->limit(7)
                ->get();
        });
    }

    public function getArticleWithMostComments(): Collection
    {
        return $this->fresh(function () {
            return Article::with(['hotness', 'image', 'category', 'tags', 'user'])->publiclyVisible()
                ->withCount('approvedComments')
                ->orderBy('approved_comments_count', 'desc')
                ->limit(3)
                ->get();
        });
    }
}
