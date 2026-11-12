<?php

namespace Modules\Front\App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Category\App\Models\Category;
use Modules\MenuBuilder\App\Models\Menu;

class StaticContentService
{
    public function composeViewData(): array
    {
        return [
            'main_nav' => [
                'menus' => $this->getMenus(),
                'categories' => $this->getCategories(),
            ],
        ];
    }

    private function getMenus(): Collection
    {
        return Menu::query()
            ->with(['category', 'children' => fn ($query) => $query->active()])
            ->active()
            ->where(function (Builder $query) {
                $query->mainMenus()
                    ->orWhere(fn (Builder $menu) => $menu->mainMenusWithChildren())
                    ->orWhere(fn (Builder $menu) => $menu->categoryMenus())
                    ->orWhere(fn (Builder $menu) => $menu->parentCategoryMenus());
            })
            ->latest('position')
            ->get();
    }

    private function getCategories(): Collection
    {
        return Category::query()
            ->parentCategories()
            ->active()
            ->withCount(['articles' => fn (Builder $query) => $query->publiclyVisible()])
            ->latest('articles_count')
            ->orderBy('name')
            ->limit(6)
            ->get();
    }
}
