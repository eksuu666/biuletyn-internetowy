<?php

namespace Modules\Front\App\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Article\App\Models\Article;
use Modules\Category\App\Models\Category;
use Modules\Tag\App\Models\Tag;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class SitemapController
{
    public function index(): Response
    {
        $index = SitemapIndex::create()->add(route('sitemap.pages'));
        $pages = (int) ceil(Article::publiclyVisible()->count() / 1000);
        for ($page = 1; $page <= $pages; $page++) {
            $index->add(route('sitemap.articles', $page));
        }

        return response($index->render(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function articles(int $page): Response
    {
        abort_if($page < 1, 404);
        $articles = Article::publiclyVisible()->orderBy('id')->forPage($page, 1000)->get();
        abort_if($articles->isEmpty(), 404);
        $sitemap = Sitemap::create();
        foreach ($articles as $article) {
            $sitemap->add(Url::create($article->getUrl())->setLastModificationDate($article->updated_at));
        }

        return response($sitemap->render(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function pages(): Response
    {
        $sitemap = Sitemap::create()->add(route('home.index'))->add(route('about-us.index'))->add(route('contact-us.index'));
        foreach (Category::active()->cursor() as $category) {
            $sitemap->add(route('categories.show', $category->slug));
        }
        foreach (Tag::active()->whereHas('articles', fn ($query) => $query->publiclyVisible())->cursor() as $tag) {
            $sitemap->add(route('tags.show', $tag->slug));
        }

        return response($sitemap->render(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
